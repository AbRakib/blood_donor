<?php

namespace Database\Seeders;

use App\Models\DonorAvailabilityLog;
use Illuminate\Database\Seeder;

class DonorAvailabilityLogSeeder extends Seeder
{
    public function run(): void
    {
        DonorAvailabilityLog::factory()->create();
    }
}
