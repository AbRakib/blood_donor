<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodDonation;
use App\Models\BloodDonor;
use App\Models\BloodRequest;
use App\Models\DonorAvailabilityLog;
use App\Models\RequestDonor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DonationController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        if (! $request->expectsJson()) {
            $common_data = (object) [
                'page_title' => __('Donation history'),
                'title' => __('Donation history'),
            ];

            return view('admin.donations')->with(compact('common_data'));
        }

        $filters = $request->validate(['blood_donor_id' => ['nullable', 'integer'], 'donation_status' => ['nullable', 'in:pending,verified']]);
        $query = BloodDonation::with('bloodDonor.bloodGroup');
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $query->where($key, $value);
            }
        }

        return $this->collectionResponse($query->orderByDesc('donated_at')->orderByDesc('id')->paginate(20), $this->formatDonation(...));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'blood_donor_id' => ['required', 'integer', Rule::exists('blood_donors', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'blood_request_id' => ['nullable', 'integer', Rule::exists('blood_requests', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'donated_at' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'bags' => ['required', 'integer', 'in:1'],
            'hospital_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        return $this->modelResponse($this->recordDonation($data)->load('bloodDonor'), $this->formatDonation(...));
    }

    public function verify(BloodDonation $donation): JsonResponse
    {
        return $this->modelResponse($this->verifyDonation($donation)->load('bloodDonor.bloodGroup'), $this->formatDonation(...));
    }

    public function history(BloodDonor $bloodDonor): JsonResponse
    {
        return $this->collectionResponse($bloodDonor->donations()->active()->where('donation_status', 'verified')->with('bloodDonor.bloodGroup')->orderByDesc('donated_at')->orderByDesc('id')->paginate(20), $this->formatDonation(...));
    }

    /** @param array<string, mixed> $data */
    private function recordDonation(array $data): BloodDonation
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
                $match = RequestDonor::query()->where('blood_request_id', $record->id)->where('blood_donor_id', $donor->id)->first();
                if ($match === null) {
                    $match = new RequestDonor;
                    $match->blood_request_id = $record->id;
                    $match->blood_donor_id = $donor->id;
                    $match->save();
                }
                if ($record->request_status === 'verified') {
                    $this->updateRequestStatus($record, 'in_progress');
                }
            }

            return $donation;
        });
    }

    private function verifyDonation(BloodDonation $donation): BloodDonation
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
            $donation->donation_status = 'verified';
            $donation->verified_at = now();
            $donation->verified_by = auth()->id();
            $donation->save();
            $verified = $donor->donations()->active()->where('donation_status', 'verified');
            $wasAvailable = $donor->is_available;
            $donor->total_donations = (clone $verified)->count();
            $donor->last_donation_date = $verified->max('donated_at');
            $donor->is_available = false;
            $donor->save();
            if ($wasAvailable) {
                $availabilityLog = new DonorAvailabilityLog;
                $availabilityLog->blood_donor_id = $donor->id;
                $availabilityLog->was_available = true;
                $availabilityLog->is_available = false;
                $availabilityLog->reason = 'Donation verified';
                $availabilityLog->save();
            }
            if ($record) {
                $match = RequestDonor::query()->where('blood_request_id', $record->id)->where('blood_donor_id', $donor->id)->first();
                if ($match === null) {
                    $match = new RequestDonor;
                    $match->blood_request_id = $record->id;
                    $match->blood_donor_id = $donor->id;
                }
                $match->response_status = 'donated';
                $match->responded_at = now();
                $match->save();
                if ($record->request_status === 'verified') {
                    $record = $this->updateRequestStatus($record, 'in_progress');
                }
                if ($record->donations()->active()->where('donation_status', 'verified')->sum('bags') >= $record->bags_needed) {
                    $this->updateRequestStatus($record, 'fulfilled');
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
