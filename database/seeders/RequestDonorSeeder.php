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
            $requestDonor = RequestDonor::withoutGlobalScopes()->where('blood_request_id', $request->id)->where('blood_donor_id', $donor->id)->first();
            $requestDonor ??= new RequestDonor;
            $requestDonor->blood_request_id = $request->id;
            $requestDonor->blood_donor_id = $donor->id;
            $requestDonor->response_status = 'pending';
            $requestDonor->save();
        }
    }
}
