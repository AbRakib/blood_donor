<?php

namespace Database\Seeders;

use App\Models\BloodRequestStatusLog;
use Illuminate\Database\Seeder;

class BloodRequestStatusLogSeeder extends Seeder
{
    public function run(): void
    {
        BloodRequestStatusLog::factory()->create();
    }
}
