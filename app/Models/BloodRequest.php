<?php

namespace App\Models;

use Database\Factories\BloodRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

#[Fillable(['blood_group_id', 'district_id', 'upazila_id', 'union_id', 'patient_name', 'contact_name', 'contact_phone', 'hospital_name', 'hospital_address', 'bags_needed', 'needed_at', 'urgency', 'request_status', 'expires_at', 'notes', 'status'])]
class BloodRequest extends DomainModel
{
    /** @use HasFactory<BloodRequestFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return array_merge(parent::casts(), ['needed_at' => 'datetime', 'expires_at' => 'datetime', 'verified_at' => 'datetime', 'bags_needed' => 'integer']);
    }

    public function bloodGroup(): BelongsTo
    {
        return $this->belongsTo(BloodGroup::class, 'blood_group_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function upazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class, 'upazila_id');
    }

    public function union(): BelongsTo
    {
        return $this->belongsTo(Union::class, 'union_id');
    }

    public function requestDonors(): HasMany
    {
        return $this->hasMany(RequestDonor::class, 'blood_request_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(BloodDonation::class, 'blood_request_id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(BloodRequestStatusLog::class, 'blood_request_id');
    }

    public function contactRequests(): HasMany
    {
        return $this->hasMany(DonorContactRequest::class, 'blood_request_id');
    }

    public function changeStatus(string $status): BloodRequest
    {
        $bloodRequest = $this;

        return DB::transaction(function () use ($bloodRequest, $status): BloodRequest {
            $record = BloodRequest::query()->active()->lockForUpdate()->findOrFail($bloodRequest->id);

            return $record->transitionTo($status);
        });
    }

    private function transitionTo(string $next): BloodRequest
    {
        $record = $this;
        $from = $record->request_status;
        if ($from === $next) {
            return $record;
        }
        $allowed = [
            'pending' => ['verified', 'cancelled', 'expired'],
            'verified' => ['in_progress', 'cancelled', 'expired'],
            'in_progress' => ['fulfilled', 'cancelled', 'expired'],
            'fulfilled' => [], 'cancelled' => [], 'expired' => [],
        ];
        if (! in_array($next, $allowed[$from] ?? [], true)) {
            throw ValidationException::withMessages(['request_status' => 'This request status transition is not allowed.']);
        }
        if (in_array($next, ['verified', 'in_progress', 'fulfilled'], true) && $record->expires_at?->isPast()) {
            throw ValidationException::withMessages(['request_status' => 'The request has expired.']);
        }
        if ($next === 'expired' && (! $record->expires_at || $record->expires_at->isFuture())) {
            throw ValidationException::withMessages(['request_status' => 'The request expiry time has not been reached.']);
        }
        if ($next === 'fulfilled' && $record->donations()->active()->verified()->sum('bags') < $record->bags_needed) {
            throw ValidationException::withMessages(['request_status' => 'The required units must be confirmed by verified donations before fulfillment.']);
        }
        if ($next === 'verified') {
            $record->verified_at = now();
            $record->verified_by = auth()->id();
        }
        $record->request_status = $next;
        $record->save();
        $record->statusLogs()->create(['from_status' => $from, 'to_status' => $next]);

        return $record;
    }

    /** @param array<string, mixed> $data */
    public static function submit(array $data): BloodRequest
    {
        return DB::transaction(function () use ($data): BloodRequest {
            $record = new BloodRequest;
            $record->blood_group_id = $data['blood_group_id'];
            $record->district_id = $data['district_id'] ?? null;
            $record->upazila_id = $data['upazila_id'] ?? null;
            $record->union_id = $data['union_id'] ?? null;
            $record->patient_name = $data['patient_name'];
            $record->contact_name = $data['contact_name'];
            $record->contact_phone = $data['contact_phone'];
            $record->hospital_name = $data['hospital_name'];
            $record->hospital_address = $data['hospital_address'] ?? null;
            $record->bags_needed = $data['bags_needed'];
            $record->needed_at = $data['needed_at'];
            $record->urgency = $data['urgency'] ?? 'normal';
            $record->expires_at = $data['expires_at'] ?? null;
            $record->notes = $data['notes'] ?? null;
            $record->status = $data['status'] ?? 1;
            $record->request_status = 'pending';
            $record->save();
            $record->refresh();
            $record->statusLogs()->create(['to_status' => 'pending']);

            return $record;
        });
    }

    /** @param array<string, mixed> $data */
    public function updateDetails(array $data): BloodRequest
    {
        $bloodRequest = $this;

        return DB::transaction(function () use ($bloodRequest, $data): BloodRequest {
            $record = BloodRequest::query()->active()->lockForUpdate()->findOrFail($bloodRequest->id);
            if (in_array($record->request_status, ['fulfilled', 'cancelled', 'expired'], true)) {
                throw ValidationException::withMessages(['request_status' => 'Closed requests cannot be edited.']);
            }
            $data += $record->only(['blood_group_id', 'district_id', 'upazila_id', 'union_id', 'patient_name', 'contact_name', 'contact_phone', 'hospital_name', 'hospital_address', 'bags_needed', 'needed_at', 'urgency', 'expires_at', 'notes', 'status']);
            if ($record->requestDonors()->exists() || $record->donations()->exists()) {
                foreach (['blood_group_id', 'bags_needed', 'district_id', 'upazila_id', 'union_id'] as $field) {
                    if ($data[$field] != $record->{$field}) {
                        throw ValidationException::withMessages([$field => 'Assigned request requirements cannot be changed.']);
                    }
                }
            }
            $next = $data['request_status'] ?? $record->request_status;
            unset($data['request_status']);
            $record->blood_group_id = $data['blood_group_id'];
            $record->district_id = $data['district_id'];
            $record->upazila_id = $data['upazila_id'];
            $record->union_id = $data['union_id'];
            $record->patient_name = $data['patient_name'];
            $record->contact_name = $data['contact_name'];
            $record->contact_phone = $data['contact_phone'];
            $record->hospital_name = $data['hospital_name'];
            $record->hospital_address = $data['hospital_address'];
            $record->bags_needed = $data['bags_needed'];
            $record->needed_at = $data['needed_at'];
            $record->urgency = $data['urgency'];
            $record->expires_at = $data['expires_at'];
            $record->notes = $data['notes'];
            $record->status = $data['status'];
            $record->save();

            return $record->transitionTo($next);
        });
    }

    /** @return Collection<int, RequestDonor> */
    public function matchDonors(): Collection
    {
        $bloodRequest = $this;

        return DB::transaction(function () use ($bloodRequest): Collection {
            $bloodRequest = BloodRequest::query()->active()->lockForUpdate()->findOrFail($bloodRequest->id);
            if (! in_array($bloodRequest->request_status, ['verified', 'in_progress'], true) || $bloodRequest->expires_at?->isPast()) {
                throw ValidationException::withMessages(['blood_request' => 'Only verified, unexpired requests can be matched.']);
            }
            $query = BloodDonor::query()->matchingFilters($bloodRequest->only(['blood_group_id', 'district_id', 'upazila_id', 'union_id']))->availableForContact();
            $query->orderBy('id')->chunkById(100, function (Collection $donors) use ($bloodRequest): void {
                foreach ($donors as $donor) {
                    $bloodRequest->requestDonors()->firstOrCreate(['blood_donor_id' => $donor->id]);
                }
            });
            if ($bloodRequest->requestDonors()->exists() && $bloodRequest->request_status === 'verified') {
                $bloodRequest->changeStatus('in_progress');
            }

            return $bloodRequest->requestDonors()->with('bloodDonor.bloodGroup')->orderBy('id')->get();
        });
    }

    public function assignDonor(BloodDonor $donor): RequestDonor
    {
        $bloodRequest = $this;

        return DB::transaction(function () use ($bloodRequest, $donor): RequestDonor {
            $record = BloodRequest::query()->active()->lockForUpdate()->findOrFail($bloodRequest->id);
            if (! in_array($record->request_status, ['verified', 'in_progress'], true) || $record->expires_at?->isPast()) {
                throw ValidationException::withMessages(['blood_request_id' => 'Verify the request before assigning donors.']);
            }
            $query = BloodDonor::query()->matchingFilters($record->only(['blood_group_id', 'district_id', 'upazila_id', 'union_id']))->availableForContact();
            if (! $query->whereKey($donor->id)->lockForUpdate()->first()) {
                throw ValidationException::withMessages(['blood_donor_id' => 'The donor must be active, available, consenting and match the request location and blood group.']);
            }
            $match = $record->requestDonors()->firstOrCreate(['blood_donor_id' => $donor->id]);
            if ($record->request_status === 'verified') {
                $record->changeStatus('in_progress');
            }

            return $match;
        });
    }

    /** @param array{response_status: string, notes?: ?string} $data */
    public function respondToDonor(int $requestDonor, array $data): RequestDonor
    {
        return $this->getConnection()->transaction(function () use ($requestDonor, $data): RequestDonor {
            $bloodRequest = BloodRequest::query()->lockForUpdate()->findOrFail($this->getKey());
            abort_unless(in_array($bloodRequest->request_status, ['verified', 'in_progress'], true) && ! $bloodRequest->expires_at?->isPast(), 422, 'The request is closed.');
            $match = $bloodRequest->requestDonors()->whereKey($requestDonor)->lockForUpdate()->firstOrFail();
            abort_if($match->response_status === 'donated', 422, 'Completed donations cannot be changed.');
            $match->response_status = $data['response_status'];
            $data += $match->only(['notes']);
            $match->notes = $data['notes'];
            $match->responded_at = now();
            $match->contacted_at = $match->contacted_at ?? now();
            $match->save();

            return $match;
        });
    }
}
