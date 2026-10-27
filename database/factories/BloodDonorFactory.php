<?php

namespace Database\Factories;

use App\Models\BloodDonor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

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

                DB::table('blood_groups')->insertOrIgnore([
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return (int) DB::table('blood_groups')->where('name', $name)->value('id');
            },
            'phone' => fake()->numerify('017########'),
            'area' => fake()->city(),
            'address' => fake()->address(),
            'last_donation_date' => null,
            'is_available' => true,
        ];
    }
}
