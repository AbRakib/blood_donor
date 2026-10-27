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

        foreach (BloodRequest::where('patient_name', 'like', 'Demo Patient %')->get() as $request) {
            BloodRequestStatusLog::withoutGlobalScopes()->updateOrCreate(
                ['blood_request_id' => $request->id, 'from_status' => null, 'to_status' => 'pending'],
                ['reason' => 'Demo request created'],
            );
        }
    }
}
