<?php

namespace Database\Seeders;

use App\Models\BloodDonor;
use App\Models\BloodGroup;
use Illuminate\Database\Seeder;

class BloodDonorSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BloodGroupSeeder::class);

        foreach (BloodGroup::orderBy('sort_order')->get() as $index => $bloodGroup) {
            BloodDonor::withoutGlobalScopes()->updateOrCreate(
                ['phone' => '017000000'.str_pad((string) $index, 2, '0', STR_PAD_LEFT)],
                ['name' => 'Demo Donor '.($index + 1), 'blood_group_id' => $bloodGroup->id, 'area' => 'Manda', 'is_available' => true, 'consent_to_contact' => true],
            );
        }
    }
}
