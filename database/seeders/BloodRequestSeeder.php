<?php

namespace Database\Seeders;

use App\Models\BloodDonor;
use App\Models\BloodRequest;
use Illuminate\Database\Seeder;

class BloodRequestSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BloodDonorSeeder::class);

        foreach (BloodDonor::where('phone', 'like', '017000000%')->get() as $donor) {
            BloodRequest::withoutGlobalScopes()->updateOrCreate(
                ['contact_phone' => $donor->phone, 'patient_name' => 'Demo Patient '.$donor->id],
                ['blood_group_id' => $donor->blood_group_id, 'contact_name' => $donor->name, 'hospital_name' => 'Manda Health Complex', 'bags_needed' => 1, 'needed_at' => now()->addDay(), 'urgency' => 'normal', 'request_status' => 'pending'],
            );
        }
    }
}
