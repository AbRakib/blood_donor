<?php

namespace Database\Seeders;

use App\Models\RequestDonor;
use Illuminate\Database\Seeder;

class RequestDonorSeeder extends Seeder
{
    public function run(): void
    {
        RequestDonor::factory()->create();
    }
}
