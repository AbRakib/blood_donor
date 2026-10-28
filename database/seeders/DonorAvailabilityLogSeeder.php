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
            $donorAvailabilityLog = DonorAvailabilityLog::withoutGlobalScopes()->where('blood_donor_id', $donor->id)->where('reason', 'Demo availability')->first();
            $donorAvailabilityLog ??= new DonorAvailabilityLog;
            $donorAvailabilityLog->blood_donor_id = $donor->id;
            $donorAvailabilityLog->reason = 'Demo availability';
            $donorAvailabilityLog->was_available = false;
            $donorAvailabilityLog->is_available = $donor->is_available;
            $donorAvailabilityLog->save();
        }
    }
}
