<?php

namespace App\Models;

use Database\Factories\UnionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['upazila_id', 'name', 'name_bn', 'status'])]
class Union extends DomainModel
{
    /** @use HasFactory<UnionFactory> */
    use HasFactory;

    public function upazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class, 'upazila_id');
    }

    public function donors(): HasMany
    {
        return $this->hasMany(BloodDonor::class, 'union_id');
    }
}
