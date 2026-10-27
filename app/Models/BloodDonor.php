<?php

namespace App\Models;

use Database\Factories\BloodDonorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'blood_group_id', 'phone', 'area', 'address', 'last_donation_date', 'is_available'])]
class BloodDonor extends Model
{
    /** @use HasFactory<BloodDonorFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'blood_group_id' => 'integer',
            'last_donation_date' => 'date',
            'is_available' => 'boolean',
            'status' => 'integer',
            'deleted' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }
}
