<?php

namespace App\Models;

use Database\Factories\UpazilaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['district_id', 'name', 'name_bn', 'status'])]
class Upazila extends DomainModel
{
    /** @use HasFactory<UpazilaFactory> */
    use HasFactory;

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function unions(): HasMany
    {
        return $this->hasMany(Union::class, 'upazila_id');
    }

    public function donors(): HasMany
    {
        return $this->hasMany(BloodDonor::class, 'upazila_id');
    }
}
