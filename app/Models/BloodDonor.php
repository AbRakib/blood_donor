<?php

namespace App\Models;

use Database\Factories\BloodDonorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'blood_group_id', 'phone', 'area', 'address', 'is_available', 'district_id', 'upazila_id', 'union_id', 'date_of_birth', 'gender', 'consent_to_contact', 'status'])]
#[Hidden(['phone'])]
class BloodDonor extends DomainModel
{
    /** @use HasFactory<BloodDonorFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return array_merge(parent::casts(), ['total_donations' => 'integer', 'last_donation_date' => 'date', 'date_of_birth' => 'date', 'is_available' => 'boolean', 'consent_to_contact' => 'boolean']);
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

    public function donations(): HasMany
    {
        return $this->hasMany(BloodDonation::class, 'blood_donor_id');
    }

    public function requestDonors(): HasMany
    {
        return $this->hasMany(RequestDonor::class, 'blood_donor_id');
    }

    public function availabilityLogs(): HasMany
    {
        return $this->hasMany(DonorAvailabilityLog::class, 'blood_donor_id');
    }

    public function contactRequests(): HasMany
    {
        return $this->hasMany(DonorContactRequest::class, 'blood_donor_id');
    }
}
