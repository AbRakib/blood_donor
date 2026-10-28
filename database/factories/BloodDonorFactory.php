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
        $gender = fake()->randomElement(['male', 'female']);
        $area = fake()->randomElement(['Prasadpur', 'Kusumba']);

        return [
            'name' => fake('bn_BD')->name($gender),
            'blood_group_id' => function (): int {
                $name = fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);

                return BloodGroup::withoutGlobalScopes()->firstOrCreate(['name' => $name])->id;
            },
            'phone' => fake()->unique()->numerify(fake()->randomElement(['013', '014', '015', '016', '017', '018', '019']).'########'),
            'area' => $area,
            'address' => $area.', Manda, Naogaon, Bangladesh',
            'date_of_birth' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'gender' => $gender,
            'last_donation_date' => null,
            'is_available' => true,
            'consent_to_contact' => true,
        ];
    }
}
