<?php

namespace Database\Seeders;

use App\Models\BloodRequest;
use Illuminate\Database\Seeder;

class BloodRequestSeeder extends Seeder
{
    public function run(): void
    {
        BloodRequest::factory()->create();
    }
}
