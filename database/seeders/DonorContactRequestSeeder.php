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

        foreach (RequestDonor::whereHas('bloodRequest', fn ($query) => $query->where('contact_phone', 'like', '017000000%'))->get() as $match) {
            $donorContactRequest = DonorContactRequest::withoutGlobalScopes()->where('blood_donor_id', $match->blood_donor_id)->where('blood_request_id', $match->blood_request_id)->where('requester_phone', '01700000100')->first();
            $donorContactRequest ??= new DonorContactRequest;
            $donorContactRequest->blood_donor_id = $match->blood_donor_id;
            $donorContactRequest->blood_request_id = $match->blood_request_id;
            $donorContactRequest->requester_phone = '01700000100';
            $donorContactRequest->requester_name = 'Demo Requester';
            $donorContactRequest->message = 'Please contact us about this blood request.';
            $donorContactRequest->contact_status = 'pending';
            $donorContactRequest->save();
        }
    }
}
