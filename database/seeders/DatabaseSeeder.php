<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BloodGroupSeeder::class);
        $this->seedSampleLocationData();
        $this->call(SystemSettingSeeder::class);
        $this->call(SettingSeeder::class);

        if (filled(config('auth.admin_password'))) {
            $this->call(AdminUserSeeder::class);
        }

        if (app()->environment(['local', 'testing'])) {
            $this->call([
                BloodDonorSeeder::class,
                BloodRequestSeeder::class,
                RequestDonorSeeder::class,
                BloodDonationSeeder::class,
                BloodRequestStatusLogSeeder::class,
                DonorAvailabilityLogSeeder::class,
            ]);
        }
    }

    private function seedSampleLocationData(): void
    {
        $divisionId = DB::table('divisions')->where('name', 'Rajshahi')->value('id');
        if ($divisionId === null) {
            $divisionId = DB::table('divisions')->insertGetId([
                'name' => 'Rajshahi',
                'name_bn' => 'রাজশাহী',
                'code' => 'BD-SAMPLE-DIV',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $districtId = DB::table('districts')->where('name', 'Naogaon')->value('id');
        if ($districtId === null) {
            $districtId = DB::table('districts')->insertGetId([
                'division_id' => $divisionId,
                'name' => 'Naogaon',
                'name_bn' => 'নওগাঁ',
                'code' => 'BD-SAMPLE-DIST',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $upazilaId = DB::table('upazilas')->where('district_id', $districtId)->where('name', 'Manda')->value('id');
        if ($upazilaId === null) {
            $upazilaId = DB::table('upazilas')->insertGetId([
                'district_id' => $districtId,
                'name' => 'Manda',
                'name_bn' => 'মান্দা',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (['Natai North', 'Natai South'] as $unionName) {
            $unionExists = DB::table('unions')->where('upazila_id', $upazilaId)->where('name', $unionName)->exists();
            if (! $unionExists) {
                DB::table('unions')->insert([
                    'upazila_id' => $upazilaId,
                    'name' => $unionName,
                    'name_bn' => $unionName === 'Natai North' ? 'নাটাই (উত্তর)' : 'নাটাই (দক্ষিন)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
