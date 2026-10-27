<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Upazila;
use Illuminate\Database\Seeder;

class UpazilaSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DistrictSeeder::class);
        Upazila::withoutGlobalScopes()->firstOrCreate(['district_id' => District::where('code', '64')->firstOrFail()->id, 'name' => 'Manda'], ['name_bn' => 'মান্দা']);
    }
}
