<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Vehicle> */
class VehicleFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'driver_id' => Driver::factory(),
            'vehicle_type' => 'ambulance',
            'vehicle_number' => fake()->unique()->bothify('DHAKA-####-####'),
            'vehicle_model' => fake()->word(),
            'vehicle_color' => fake()->safeColorName(),
            'status' => 1,
        ];
    }
}
