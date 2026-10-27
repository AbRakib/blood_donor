<?php

namespace Database\Factories;

use App\Models\BloodDonation;
use App\Models\BloodDonor;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BloodDonation> */
class BloodDonationFactory extends Factory
{
    public function definition(): array
    {
        return ['blood_donor_id' => BloodDonor::factory(), 'blood_request_id' => null, 'donated_at' => today(), 'bags' => 1];
    }
}
