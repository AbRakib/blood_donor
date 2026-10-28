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
            $activityLog = ActivityLog::withoutGlobalScopes()->where('subject_type', $donor->getTable())->where('subject_id', $donor->id)->where('action', 'created')->first();
            $activityLog ??= new ActivityLog;
            $activityLog->subject_type = $donor->getTable();
            $activityLog->subject_id = $donor->id;
            $activityLog->action = 'created';
            $activityLog->user_id = null;
            $activityLog->save();
        }
    }
}
