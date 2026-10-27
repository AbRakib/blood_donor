<?php

namespace Database\Factories;

use App\Models\BloodDonor;
use App\Models\BloodRequest;
use App\Models\RequestDonor;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RequestDonor> */
class RequestDonorFactory extends Factory
{
    public function definition(): array
    {
        return ['blood_request_id' => BloodRequest::factory(), 'blood_donor_id' => BloodDonor::factory(), 'response_status' => 'pending'];
    }
}
