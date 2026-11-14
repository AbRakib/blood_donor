<?php

namespace App\Models;

use Database\Factories\DriverFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'phone', 'email', 'photo', 'date_of_birth', 'gender',
    'present_address', 'permanent_address', 'city', 'district',
    'nid_number', 'nid_front_image', 'nid_back_image',
    'license_number', 'license_type', 'license_issue_date', 'license_expiry_date', 'license_image',
    'status',
])]
class Driver extends DomainModel
{
    /** @use HasFactory<DriverFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'date_of_birth' => 'date',
            'license_issue_date' => 'date',
            'license_expiry_date' => 'date',
        ]);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
