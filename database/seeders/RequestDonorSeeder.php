<?php

namespace Database\Seeders;

use App\Models\BloodDonor;
use App\Models\BloodRequest;
use App\Models\RequestDonor;
use Illuminate\Database\Seeder;

class RequestDonorSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BloodRequestSeeder::class);

        foreach (BloodRequest::where('contact_phone', 'like', '017000000%')->get() as $request) {
            $donor = BloodDonor::where('phone', $request->contact_phone)->firstOrFail();
            RequestDonor::withoutGlobalScopes()->updateOrCreate(
                ['blood_request_id' => $request->id, 'blood_donor_id' => $donor->id],
                ['response_status' => 'pending'],
            );
        }
    }
}
