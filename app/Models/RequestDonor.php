<?php

namespace App\Models;

use Database\Factories\RequestDonorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['blood_request_id', 'blood_donor_id', 'response_status', 'contacted_at', 'responded_at', 'notes', 'status'])]
class RequestDonor extends DomainModel
{
    /** @use HasFactory<RequestDonorFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return array_merge(parent::casts(), ['contacted_at' => 'datetime', 'responded_at' => 'datetime']);
    }

    public function bloodRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class, 'blood_request_id');
    }

    public function bloodDonor(): BelongsTo
    {
        return $this->belongsTo(BloodDonor::class, 'blood_donor_id');
    }
}
