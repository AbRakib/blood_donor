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
}
