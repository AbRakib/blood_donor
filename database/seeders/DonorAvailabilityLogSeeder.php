<?php

namespace Database\Seeders;

use App\Models\BloodDonor;
use App\Models\DonorAvailabilityLog;
use Illuminate\Database\Seeder;

class DonorAvailabilityLogSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BloodDonorSeeder::class);

        foreach (BloodDonor::where('phone', 'like', '017000000%')->get() as $donor) {
            DonorAvailabilityLog::withoutGlobalScopes()->updateOrCreate(
                ['blood_donor_id' => $donor->id, 'reason' => 'Demo availability'],
                ['was_available' => false, 'is_available' => $donor->is_available],
            );
        }
    }
}
