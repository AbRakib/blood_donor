<?php

namespace Database\Factories;

use App\Models\BloodDonor;
use App\Models\DonorAvailabilityLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DonorAvailabilityLog> */
class DonorAvailabilityLogFactory extends Factory
{
    public function definition(): array
    {
        return ['blood_donor_id' => BloodDonor::factory(), 'was_available' => true, 'is_available' => false];
    }
}
