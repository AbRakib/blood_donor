<?php

namespace Database\Factories;

use App\Models\Union;
use App\Models\Upazila;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Union> */
class UnionFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->unique()->city(), 'upazila_id' => Upazila::factory()];
    }
}
