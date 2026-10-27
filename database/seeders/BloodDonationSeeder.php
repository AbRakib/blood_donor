<?php

namespace Database\Seeders;

use App\Models\BloodDonation;
use Illuminate\Database\Seeder;

class BloodDonationSeeder extends Seeder
{
    public function run(): void
    {
        BloodDonation::factory()->create();
    }
}
