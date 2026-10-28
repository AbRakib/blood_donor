<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $district = District::withoutGlobalScopes()->where('code', '64')->first();
        $district ??= new District;
        $district->code = '64';
        $district->name = 'Naogaon';
        $district->name_bn = 'নওগাঁ';
        $district->save();
    }
}
