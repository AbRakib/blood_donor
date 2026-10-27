<?php

namespace App\Models;

use Database\Factories\BloodDonationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
