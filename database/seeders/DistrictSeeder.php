<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        District::withoutGlobalScopes()->firstOrCreate(['code' => '64'], ['name' => 'Naogaon', 'name_bn' => 'নওগাঁ']);
    }
}
