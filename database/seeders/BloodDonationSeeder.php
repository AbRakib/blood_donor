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
            $bloodDonation = BloodDonation::withoutGlobalScopes()->where('blood_request_id', $match->blood_request_id)->where('blood_donor_id', $match->blood_donor_id)->first();
            $bloodDonation ??= new BloodDonation;
            $bloodDonation->blood_request_id = $match->blood_request_id;
            $bloodDonation->blood_donor_id = $match->blood_donor_id;
            $bloodDonation->donated_at = today();
            $bloodDonation->bags = 1;
            $bloodDonation->hospital_name = $match->bloodRequest->hospital_name;
            $bloodDonation->save();
        }
    }
}
