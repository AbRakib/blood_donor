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
        $hospital = fake()->randomElement([
            ['name' => 'Manda Upazila Health Complex', 'address' => 'Manda, Naogaon, Bangladesh'],
            ['name' => 'Naogaon General Hospital', 'address' => 'Naogaon Sadar, Naogaon, Bangladesh'],
            ['name' => 'Rajshahi Medical College Hospital', 'address' => 'Rajpara, Rajshahi, Bangladesh'],
        ]);
        $neededAt = now()->addHours(fake()->numberBetween(2, 48));

        return [
            'blood_group_id' => function (): int {
                $name = fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);
                $bloodGroup = BloodGroup::withoutGlobalScopes()->where('name', $name)->first();
                if ($bloodGroup === null) {
                    $bloodGroup = new BloodGroup;
                    $bloodGroup->name = $name;
                    $bloodGroup->save();
                }

                return $bloodGroup->id;
            },
            'patient_name' => fake('bn_BD')->name(),
            'contact_name' => fake('bn_BD')->name(),
            'contact_phone' => fake()->numerify('01'.fake()->numberBetween(3, 9).'########'),
            'hospital_name' => $hospital['name'],
            'hospital_address' => $hospital['address'],
            'bags_needed' => fake()->numberBetween(1, 3),
            'needed_at' => $neededAt,
            'expires_at' => $neededAt->copy()->addDay(),
            'urgency' => fake()->randomElement(['normal', 'urgent', 'emergency', 'critical']),
            'request_status' => 'pending',
            'notes' => fake()->randomElement([
                'Blood is needed for a patient scheduled for surgery. Please contact the attendant before visiting.',
                'A patient receiving treatment for thalassemia needs a blood transfusion. Please call to coordinate donation.',
                'Blood is needed for a maternity patient. The family can help coordinate with the hospital blood bank.',
                'A patient being treated for severe anemia needs blood. Please contact the family for donation arrangements.',
            ]),
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (): array => ['request_status' => 'verified', 'verified_at' => now()]);
    }
}
