<?php

namespace Database\Seeders;

use App\Models\DonorContactRequest;
use App\Models\RequestDonor;
use Illuminate\Database\Seeder;

class DonorContactRequestSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RequestDonorSeeder::class);

        foreach (RequestDonor::whereHas('bloodRequest', fn ($query) => $query->where('patient_name', 'like', 'Demo Patient %'))->get() as $match) {
            DonorContactRequest::withoutGlobalScopes()->updateOrCreate(
                ['blood_donor_id' => $match->blood_donor_id, 'blood_request_id' => $match->blood_request_id, 'requester_phone' => '01700000100'],
                ['requester_name' => 'Demo Requester', 'message' => 'Please contact us about this blood request.', 'contact_status' => 'pending'],
            );
        }
    }
}
