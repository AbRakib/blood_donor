<?php

namespace App\Services;

use App\Models\BloodRequest;
use App\Models\BloodRequestStatusLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BloodRequestService
{
    /** @param array<string, mixed> $data */
    public function create(array $data): BloodRequest
    {
        return DB::transaction(function () use ($data): BloodRequest {
            $record = BloodRequest::create(array_merge($data, ['request_status' => 'pending']))->refresh();
            BloodRequestStatusLog::create(['blood_request_id' => $record->id, 'to_status' => 'pending']);

            return $record;
        });
    }

    /** @param array<string, mixed> $data */
    public function update(BloodRequest $bloodRequest, array $data): BloodRequest
    {
        return DB::transaction(function () use ($bloodRequest, $data): BloodRequest {
            $record = BloodRequest::query()->active()->lockForUpdate()->findOrFail($bloodRequest->id);
            if (in_array($record->request_status, ['fulfilled', 'cancelled', 'expired'], true)) {
                throw ValidationException::withMessages(['request_status' => 'Closed requests cannot be edited.']);
            }
            if ($record->requestDonors()->exists() || $record->donations()->exists()) {
                foreach (['blood_group_id', 'bags_needed', 'district_id', 'upazila_id', 'union_id'] as $field) {
                    if (array_key_exists($field, $data) && $data[$field] != $record->{$field}) {
                        throw ValidationException::withMessages([$field => 'Assigned request requirements cannot be changed.']);
                    }
                }
            }
            $next = $data['request_status'] ?? $record->request_status;
            unset($data['request_status']);
            $record->update($data);

            return $this->transition($record, $next);
        });
    }

    public function changeStatus(BloodRequest $bloodRequest, string $status): BloodRequest
    {
        return DB::transaction(function () use ($bloodRequest, $status): BloodRequest {
            $record = BloodRequest::query()->active()->lockForUpdate()->findOrFail($bloodRequest->id);

            return $this->transition($record, $status);
        });
    }

    public function verify(BloodRequest $bloodRequest): BloodRequest
    {
        return $this->changeStatus($bloodRequest, 'verified');
    }

    private function transition(BloodRequest $record, string $next): BloodRequest
    {
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
        if ($next === 'fulfilled' && $record->donations()->active()->where('donation_status', 'verified')->sum('bags') < $record->bags_needed) {
            throw ValidationException::withMessages(['request_status' => 'The required units must be confirmed by verified donations before fulfillment.']);
        }
        if ($next === 'verified') {
            $record->forceFill(['verified_at' => now(), 'verified_by' => auth()->id()]);
        }
        $record->request_status = $next;
        $record->save();
        BloodRequestStatusLog::create(['blood_request_id' => $record->id, 'from_status' => $from, 'to_status' => $next]);

        return $record;
    }
}
