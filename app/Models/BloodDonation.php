<?php

namespace App\Models;

use Database\Factories\BloodDonationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

#[Fillable(['blood_donor_id', 'blood_request_id', 'donated_at', 'bags', 'hospital_name', 'notes', 'status'])]
class BloodDonation extends DomainModel
{
    /** @use HasFactory<BloodDonationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return array_merge(parent::casts(), ['donation_status' => 'string', 'verified_at' => 'datetime', 'donated_at' => 'date', 'bags' => 'integer']);
    }

    public function bloodDonor(): BelongsTo
    {
        return $this->belongsTo(BloodDonor::class, 'blood_donor_id');
    }

    public function bloodRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class, 'blood_request_id');
    }

    #[Scope]
    protected function verified(Builder $query): Builder
    {
        return $query->where('donation_status', 'verified');
    }

    /** @param array<string, mixed> $data */
    public static function record(array $data): BloodDonation
    {
        return DB::transaction(function () use ($data): BloodDonation {
            $record = ! empty($data['blood_request_id']) ? BloodRequest::query()->active()->lockForUpdate()->findOrFail($data['blood_request_id']) : null;
            $donor = BloodDonor::query()->active()->lockForUpdate()->findOrFail($data['blood_donor_id']);
            if ($record) {
                self::validateRequest($record, $donor);
                if ($record->donations()->where('blood_donor_id', $donor->id)->exists()) {
                    throw ValidationException::withMessages(['blood_donor_id' => 'This donor already has a donation recorded for this request.']);
                }
            }
            if ($donor->donations()->whereDate('donated_at', $data['donated_at'])->exists()) {
                throw ValidationException::withMessages(['donated_at' => 'A donation is already recorded for this donor on this date.']);
            }
            $donation = new BloodDonation;
            $donation->blood_donor_id = $data['blood_donor_id'];
            $donation->blood_request_id = $data['blood_request_id'] ?? null;
            $donation->donated_at = $data['donated_at'];
            $donation->bags = $data['bags'];
            $donation->hospital_name = $data['hospital_name'] ?? null;
            $donation->notes = $data['notes'] ?? null;
            $donation->status = $data['status'] ?? 1;
            $donation->save();
            $donation->refresh();
            if ($record) {
                $match = $record->requestDonors()->firstOrCreate(['blood_donor_id' => $donor->id]);
                if ($record->request_status === 'verified') {
                    $record->changeStatus('in_progress');
                }
            }

            return $donation;
        });
    }

    public function verify(): BloodDonation
    {
        $donation = $this;

        return DB::transaction(function () use ($donation): BloodDonation {
            $record = $donation->blood_request_id ? BloodRequest::query()->active()->lockForUpdate()->findOrFail($donation->blood_request_id) : null;
            $donor = BloodDonor::query()->active()->lockForUpdate()->findOrFail($donation->blood_donor_id);
            $donation = BloodDonation::query()->active()->lockForUpdate()->findOrFail($donation->id);
            if ($donation->donation_status === 'verified') {
                return $donation;
            }
            if ($record) {
                self::validateRequest($record, $donor);
                $received = $record->donations()->active()->verified()->sum('bags');
                if ($received + $donation->bags > $record->bags_needed) {
                    throw ValidationException::withMessages(['bags' => 'Verified units would exceed the requested units.']);
                }
            }
            $donation->donation_status = 'verified';
            $donation->verified_at = now();
            $donation->verified_by = auth()->id();
            $donation->save();
            $verified = $donor->donations()->active()->verified();
            $wasAvailable = $donor->is_available;
            $donor->total_donations = (clone $verified)->count();
            $donor->last_donation_date = $verified->max('donated_at');
            $donor->is_available = false;
            $donor->save();
            $donor->logAvailabilityChange($wasAvailable, 'Donation verified');
            if ($record) {
                $match = $record->requestDonors()->firstOrCreate(['blood_donor_id' => $donor->id]);
                $match->response_status = 'donated';
                $match->responded_at = now();
                $match->save();
                if ($record->request_status === 'verified') {
                    $record = $record->changeStatus('in_progress');
                }
                if ($record->donations()->active()->verified()->sum('bags') >= $record->bags_needed) {
                    $record->changeStatus('fulfilled');
                }
            }

            return $donation;
        });
    }

    private static function validateRequest(BloodRequest $record, BloodDonor $donor): void
    {
        if (! in_array($record->request_status, ['verified', 'in_progress'], true) || $record->expires_at?->isPast() || $record->blood_group_id !== $donor->blood_group_id) {
            throw ValidationException::withMessages(['blood_request_id' => 'The request must be verified, open, unexpired and match the donor blood group.']);
        }
    }
}
