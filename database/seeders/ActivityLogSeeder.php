<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\BloodDonor;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BloodDonorSeeder::class);

        foreach (BloodDonor::where('phone', 'like', '017000000%')->get() as $donor) {
            ActivityLog::withoutGlobalScopes()->updateOrCreate(
                ['subject_type' => $donor->getTable(), 'subject_id' => $donor->id, 'action' => 'created'],
                ['user_id' => null],
            );
        }
    }
}
