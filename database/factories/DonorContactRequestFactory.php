<?php

namespace Database\Factories;

use App\Models\BloodDonor;
use App\Models\DonorContactRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DonorContactRequest> */
class DonorContactRequestFactory extends Factory
{
    public function definition(): array
    {
        return ['blood_donor_id' => BloodDonor::factory(), 'requester_name' => fake()->name(), 'requester_phone' => fake()->numerify('017########'), 'message' => fake()->sentence(), 'contact_status' => 'pending'];
    }
}
