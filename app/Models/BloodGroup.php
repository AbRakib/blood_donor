<?php

namespace App\Models;

use Database\Factories\BloodGroupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'sort_order', 'status'])]
class BloodGroup extends DomainModel
{
    /** @use HasFactory<BloodGroupFactory> */
    use HasFactory;

    public function donors(): HasMany
    {
        return $this->hasMany(BloodDonor::class, 'blood_group_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(BloodRequest::class, 'blood_group_id');
    }
}
