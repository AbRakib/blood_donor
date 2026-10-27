<?php

namespace App\Models;

use Database\Factories\BloodRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
