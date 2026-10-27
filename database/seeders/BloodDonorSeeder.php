<?php

namespace Database\Seeders;

use App\Models\BloodDonor;
use Illuminate\Database\Seeder;

class BloodDonorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BloodDonor::factory()->count(10)->create();
    }
}
