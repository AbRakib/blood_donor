<?php

namespace Database\Factories;

use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<District> */
class DistrictFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->unique()->city(), 'name_bn' => null, 'code' => fake()->unique()->numerify('D####')];
    }
}
