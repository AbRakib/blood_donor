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
                ['contact_phone' => $donor->phone],
                BloodRequest::factory()->raw([
                    'blood_group_id' => $donor->blood_group_id,
                    'district_id' => $donor->district_id,
                    'upazila_id' => $donor->upazila_id,
                    'union_id' => $donor->union_id,
                    'contact_name' => $donor->name,
                    'contact_phone' => $donor->phone,
                ]),
            );
        }
    }
}
