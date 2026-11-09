<?php

namespace Database\Seeders;

use App\Models\BloodDonation;
use App\Models\BloodDonor;
use App\Models\BloodRequest;
use Illuminate\Database\Seeder;

class BloodDonationSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([BloodDonorSeeder::class, BloodRequestSeeder::class]);

        $requests = BloodRequest::query()->where('contact_phone', 'like', '017000000%')->orderBy('id')->get();
        foreach ($requests as $index => $request) {
            $donor = BloodDonor::query()->where('blood_group_id', $request->blood_group_id)->where('phone', 'like', '017000000%')->orderBy('id')->skip($index % 8)->first();
            $donor ??= BloodDonor::query()->where('phone', 'like', '017000000%')->orderBy('id')->firstOrFail();

            BloodDonation::query()->firstOrCreate([
                'blood_donor_id' => $donor->id,
                'blood_request_id' => $request->id,
            ], [
                'donated_at' => today(),
                'bags' => 1,
                'status' => 1,
            ]);
        }
    }
}
