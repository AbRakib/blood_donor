<?php

namespace App\Models;

use Database\Factories\DonorAvailabilityLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['blood_donor_id', 'was_available', 'is_available', 'reason'])]
class DonorAvailabilityLog extends DomainModel
{
    /** @use HasFactory<DonorAvailabilityLogFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return array_merge(parent::casts(), ['was_available' => 'boolean', 'is_available' => 'boolean']);
    }

    public function bloodDonor(): BelongsTo
    {
        return $this->belongsTo(BloodDonor::class, 'blood_donor_id');
    }
}
