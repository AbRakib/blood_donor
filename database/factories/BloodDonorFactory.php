<?php

namespace Database\Factories;

use App\Models\BloodDonor;
use App\Models\BloodGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BloodDonor>
 */
class BloodDonorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'blood_group_id' => function (): int {
                $name = fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);

                return BloodGroup::withoutGlobalScopes()->firstOrCreate(['name' => $name])->id;
            },
            'phone' => fake()->numerify('017########'),
            'area' => fake()->city(),
            'address' => fake()->address(),
            'last_donation_date' => null,
            'is_available' => true,
            'consent_to_contact' => true,
        ];
    }
}
