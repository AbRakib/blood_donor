<?php

namespace App\Services;

use App\Models\BloodDonor;
use App\Models\BloodRequest;
use App\Models\RequestDonor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DonorMatchingService
{
    public function __construct(private DonorSearchService $search, private BloodRequestService $requests) {}

    /** @return Collection<int, RequestDonor> */
    public function match(BloodRequest $bloodRequest): Collection
    {
        return DB::transaction(function () use ($bloodRequest): Collection {
            $bloodRequest = BloodRequest::query()->active()->lockForUpdate()->findOrFail($bloodRequest->id);
            if (! in_array($bloodRequest->request_status, ['verified', 'in_progress'], true) || $bloodRequest->expires_at?->isPast()) {
                throw ValidationException::withMessages(['blood_request' => 'Only verified, unexpired requests can be matched.']);
            }
            $query = $this->search->available($this->search->query($bloodRequest->only(['blood_group_id', 'district_id', 'upazila_id', 'union_id'])));
            $query->orderBy('id')->chunkById(100, function (Collection $donors) use ($bloodRequest): void {
                foreach ($donors as $donor) {
                    RequestDonor::firstOrCreate(['blood_request_id' => $bloodRequest->id, 'blood_donor_id' => $donor->id]);
                }
            });
            if ($bloodRequest->requestDonors()->exists() && $bloodRequest->request_status === 'verified') {
                $this->requests->changeStatus($bloodRequest, 'in_progress');
            }

            return $bloodRequest->requestDonors()->with('bloodDonor.bloodGroup')->orderBy('id')->get();
        });
    }

    public function assign(BloodRequest $bloodRequest, BloodDonor $donor): RequestDonor
    {
        return DB::transaction(function () use ($bloodRequest, $donor): RequestDonor {
            $record = BloodRequest::query()->active()->lockForUpdate()->findOrFail($bloodRequest->id);
            if (! in_array($record->request_status, ['verified', 'in_progress'], true) || $record->expires_at?->isPast()) {
                throw ValidationException::withMessages(['blood_request_id' => 'Verify the request before assigning donors.']);
            }
            $query = $this->search->available($this->search->query($record->only(['blood_group_id', 'district_id', 'upazila_id', 'union_id'])));
            if (! $query->whereKey($donor->id)->lockForUpdate()->first()) {
                throw ValidationException::withMessages(['blood_donor_id' => 'The donor must be active, available, consenting and match the request location and blood group.']);
            }
            $match = RequestDonor::firstOrCreate(['blood_request_id' => $record->id, 'blood_donor_id' => $donor->id]);
            if ($record->request_status === 'verified') {
                $this->requests->changeStatus($record, 'in_progress');
            }

            return $match;
        });
    }
}
