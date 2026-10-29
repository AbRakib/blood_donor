<?php

namespace App\Models;

use Database\Factories\DonorContactRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['blood_donor_id', 'blood_request_id', 'requester_name', 'requester_phone', 'message', 'contact_status', 'handled_at'])]
class DonorContactRequest extends DomainModel
{
    /** @use HasFactory<DonorContactRequestFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return array_merge(parent::casts(), ['handled_at' => 'datetime']);
    }

    public function bloodDonor(): BelongsTo
    {
        return $this->belongsTo(BloodDonor::class, 'blood_donor_id');
    }

    public function bloodRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class, 'blood_request_id');
    }

    public function changeStatus(string $status): DonorContactRequest
    {
        return $this->getConnection()->transaction(function () use ($status): DonorContactRequest {
            $contactRequest = DonorContactRequest::query()->lockForUpdate()->findOrFail($this->getKey());
            $allowed = ['pending' => ['approved', 'rejected'], 'approved' => ['completed', 'rejected'], 'rejected' => [], 'completed' => []];
            abort_unless(in_array($status, $allowed[$contactRequest->contact_status], true), 422, 'This contact status transition is not allowed.');
            if ($status === 'approved') {
                $donor = $contactRequest->bloodDonor()->active()->availableForContact()->first();
                abort_unless($donor, 422, 'The donor is no longer available for contact.');
            }
            $contactRequest->contact_status = $status;
            $contactRequest->handled_at = now();
            $contactRequest->save();

            return $contactRequest;
        });
    }

    /** @param array{blood_donor_id: int, requester_name: string, requester_phone: string, message: string} $data */
    public static function submit(array $data): DonorContactRequest
    {
        return (new static)->getConnection()->transaction(function () use ($data): DonorContactRequest {
            $donor = BloodDonor::query()->active()->availableForContact()->lockForUpdate()->findOrFail($data['blood_donor_id']);
            $contactRequest = new DonorContactRequest;
            $contactRequest->blood_donor_id = $donor->id;
            $contactRequest->requester_name = $data['requester_name'];
            $contactRequest->requester_phone = $data['requester_phone'];
            $contactRequest->message = $data['message'];
            $contactRequest->save();

            return $contactRequest;
        });
    }
}
