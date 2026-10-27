<?php

namespace App\Services;

use App\Models\BloodDonation;
use App\Models\BloodDonor;
use App\Models\BloodRequest;
use App\Models\DonorAvailabilityLog;
use App\Models\RequestDonor;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DonationService
{
    public function __construct(private BloodRequestService $requests) {}

    /** @param array<string, mixed> $data */
    public function record(array $data): BloodDonation
    {
        return DB::transaction(function () use ($data): BloodDonation {
            $record = ! empty($data['blood_request_id']) ? BloodRequest::query()->active()->lockForUpdate()->findOrFail($data['blood_request_id']) : null;
            $donor = BloodDonor::query()->active()->lockForUpdate()->findOrFail($data['blood_donor_id']);
            if ($record) {
                $this->validateRequest($record, $donor);
                if ($record->donations()->where('blood_donor_id', $donor->id)->exists()) {
                    throw ValidationException::withMessages(['blood_donor_id' => 'This donor already has a donation recorded for this request.']);
                }
            }
            if ($donor->donations()->whereDate('donated_at', $data['donated_at'])->exists()) {
                throw ValidationException::withMessages(['donated_at' => 'A donation is already recorded for this donor on this date.']);
            }
            $donation = BloodDonation::create($data)->refresh();
            if ($record) {
                RequestDonor::firstOrCreate(['blood_request_id' => $record->id, 'blood_donor_id' => $donor->id]);
                if ($record->request_status === 'verified') {
                    $this->requests->changeStatus($record, 'in_progress');
                }
            }

            return $donation;
        });
    }

    public function verify(BloodDonation $donation): BloodDonation
    {
        return DB::transaction(function () use ($donation): BloodDonation {
            $record = $donation->blood_request_id ? BloodRequest::query()->active()->lockForUpdate()->findOrFail($donation->blood_request_id) : null;
            $donor = BloodDonor::query()->active()->lockForUpdate()->findOrFail($donation->blood_donor_id);
            $donation = BloodDonation::query()->active()->lockForUpdate()->findOrFail($donation->id);
            if ($donation->donation_status === 'verified') {
                return $donation;
            }
            if ($record) {
                $this->validateRequest($record, $donor);
                $received = $record->donations()->active()->where('donation_status', 'verified')->sum('bags');
                if ($received + $donation->bags > $record->bags_needed) {
                    throw ValidationException::withMessages(['bags' => 'Verified units would exceed the requested units.']);
                }
            }
            $donation->forceFill(['donation_status' => 'verified', 'verified_at' => now(), 'verified_by' => auth()->id()])->save();
            $verified = $donor->donations()->active()->where('donation_status', 'verified');
            $wasAvailable = $donor->is_available;
            $donor->forceFill(['total_donations' => (clone $verified)->count(), 'last_donation_date' => $verified->max('donated_at'), 'is_available' => false])->save();
            if ($wasAvailable) {
                DonorAvailabilityLog::create(['blood_donor_id' => $donor->id, 'was_available' => true, 'is_available' => false, 'reason' => 'Donation verified']);
            }
            if ($record) {
                RequestDonor::updateOrCreate(['blood_request_id' => $record->id, 'blood_donor_id' => $donor->id], ['response_status' => 'donated', 'responded_at' => now()]);
                if ($record->request_status === 'verified') {
                    $record = $this->requests->changeStatus($record, 'in_progress');
                }
                if ($record->donations()->active()->where('donation_status', 'verified')->sum('bags') >= $record->bags_needed) {
                    $this->requests->changeStatus($record, 'fulfilled');
                }
            }

            return $donation;
        });
    }

    private function validateRequest(BloodRequest $record, BloodDonor $donor): void
    {
        if (! in_array($record->request_status, ['verified', 'in_progress'], true) || $record->expires_at?->isPast() || $record->blood_group_id !== $donor->blood_group_id) {
            throw ValidationException::withMessages(['blood_request_id' => 'The request must be verified, open, unexpired and match the donor blood group.']);
        }
    }
}
