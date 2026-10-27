<?php

namespace Database\Factories;

use App\Models\BloodRequest;
use App\Models\BloodRequestStatusLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BloodRequestStatusLog> */
class BloodRequestStatusLogFactory extends Factory
{
    public function definition(): array
    {
        return ['blood_request_id' => BloodRequest::factory(), 'from_status' => null, 'to_status' => 'pending'];
    }
}
