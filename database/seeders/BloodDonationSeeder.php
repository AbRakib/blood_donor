<?php

namespace Database\Seeders;

use App\Models\BloodDonation;
use App\Models\RequestDonor;
use Illuminate\Database\Seeder;

class BloodDonationSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RequestDonorSeeder::class);

        foreach (RequestDonor::whereHas('bloodRequest', fn ($query) => $query->where('patient_name', 'like', 'Demo Patient %'))->get() as $match) {
            BloodDonation::withoutGlobalScopes()->updateOrCreate(
                ['blood_request_id' => $match->blood_request_id, 'blood_donor_id' => $match->blood_donor_id],
                ['donated_at' => today(), 'bags' => 1, 'hospital_name' => 'Manda Health Complex'],
            );
        }
    }
}
