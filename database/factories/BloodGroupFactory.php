<?php

namespace Database\Factories;

use App\Models\BloodGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BloodGroup> */
class BloodGroupFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->unique()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']), 'sort_order' => 0];
    }
}
