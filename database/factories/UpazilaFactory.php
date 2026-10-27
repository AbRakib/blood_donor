<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Upazila;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Upazila> */
class UpazilaFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->unique()->city(), 'district_id' => District::factory()];
    }
}
