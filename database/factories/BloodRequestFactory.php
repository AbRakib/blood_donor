<?php

namespace Database\Factories;

use App\Models\BloodGroup;
use App\Models\BloodRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BloodRequest> */
class BloodRequestFactory extends Factory
{
    public function definition(): array
    {
        return ['blood_group_id' => BloodGroup::factory(), 'patient_name' => fake()->name(), 'contact_name' => fake()->name(), 'contact_phone' => fake()->numerify('017########'), 'hospital_name' => fake()->company(), 'bags_needed' => 1, 'needed_at' => now()->addDay(), 'urgency' => 'normal', 'request_status' => 'pending'];
    }

    public function verified(): static
    {
        return $this->state(fn (): array => ['request_status' => 'verified', 'verified_at' => now()]);
    }
}
