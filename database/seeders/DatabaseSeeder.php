<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([AdminUserSeeder::class, BloodGroupSeeder::class, UnionSeeder::class, SystemSettingSeeder::class]);

        if (app()->environment(['local', 'testing'])) {
            $this->call([
                BloodDonationSeeder::class,
                BloodRequestStatusLogSeeder::class,
                DonorAvailabilityLogSeeder::class,
                DonorContactRequestSeeder::class,
            ]);
        }
    }
}
