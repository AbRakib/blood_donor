<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\BloodDonor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ActivityLog> */
class ActivityLogFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'action' => 'created', 'subject_type' => 'blood_donors', 'subject_id' => BloodDonor::factory()];
    }
}
