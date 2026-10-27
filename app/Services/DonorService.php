<?php

namespace App\Services;

use App\Models\BloodDonor;
use App\Models\DonorAvailabilityLog;
use Illuminate\Support\Facades\DB;

class DonorService
{
    /** @param array<string, mixed> $data */
    public function create(array $data): BloodDonor
    {
        return DB::transaction(fn (): BloodDonor => BloodDonor::create($data)->refresh());
    }

    /** @param array<string, mixed> $data */
    public function update(BloodDonor $donor, array $data): BloodDonor
    {
        return DB::transaction(function () use ($donor, $data): BloodDonor {
            $donor = BloodDonor::query()->lockForUpdate()->findOrFail($donor->id);
            $wasAvailable = $donor->is_available;
            $donor->update($data);
            if ($wasAvailable !== $donor->is_available) {
                DonorAvailabilityLog::create(['blood_donor_id' => $donor->id, 'was_available' => $wasAvailable, 'is_available' => $donor->is_available]);
            }

            return $donor;
        });
    }
}
