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

        foreach (RequestDonor::with('bloodRequest')->whereHas('bloodRequest', fn ($query) => $query->where('contact_phone', 'like', '017000000%'))->get() as $match) {
            BloodDonation::withoutGlobalScopes()->updateOrCreate(
                ['blood_request_id' => $match->blood_request_id, 'blood_donor_id' => $match->blood_donor_id],
                ['donated_at' => today(), 'bags' => 1, 'hospital_name' => $match->bloodRequest->hospital_name],
            );
        }
    }
}
