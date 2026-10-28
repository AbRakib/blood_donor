<?php

namespace Database\Seeders;

use App\Models\BloodRequest;
use App\Models\BloodRequestStatusLog;
use Illuminate\Database\Seeder;

class BloodRequestStatusLogSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BloodRequestSeeder::class);

        foreach (BloodRequest::where('contact_phone', 'like', '017000000%')->get() as $request) {
            $bloodRequestStatusLog = BloodRequestStatusLog::withoutGlobalScopes()->where('blood_request_id', $request->id)->where('from_status', null)->where('to_status', 'pending')->first();
            $bloodRequestStatusLog ??= new BloodRequestStatusLog;
            $bloodRequestStatusLog->blood_request_id = $request->id;
            $bloodRequestStatusLog->from_status = null;
            $bloodRequestStatusLog->to_status = 'pending';
            $bloodRequestStatusLog->reason = 'Demo request created';
            $bloodRequestStatusLog->save();
        }
    }
}
