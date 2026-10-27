<?php

namespace App\Models;

use Database\Factories\DistrictFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'name_bn', 'code', 'status'])]
class District extends DomainModel
{
    /** @use HasFactory<DistrictFactory> */
    use HasFactory;

    public function upazilas(): HasMany
    {
        return $this->hasMany(Upazila::class, 'district_id');
    }

    public function donors(): HasMany
    {
        return $this->hasMany(BloodDonor::class, 'district_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(BloodRequest::class, 'district_id');
    }
}
