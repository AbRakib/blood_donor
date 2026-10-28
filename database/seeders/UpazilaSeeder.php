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
        $upazila = Upazila::withoutGlobalScopes()->where('district_id', District::where('code', '64')->firstOrFail()->id)->where('name', 'Manda')->first();
        $upazila ??= new Upazila;
        $upazila->district_id = District::where('code', '64')->firstOrFail()->id;
        $upazila->name = 'Manda';
        $upazila->name_bn = 'মান্দা';
        $upazila->save();
    }
}
