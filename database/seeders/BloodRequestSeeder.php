<?php

namespace Database\Seeders;

use App\Models\BloodDonor;
use App\Models\BloodRequest;
use Illuminate\Database\Seeder;

class BloodRequestSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BloodDonorSeeder::class);

        foreach (BloodDonor::where('phone', 'like', '017000000%')->get() as $donor) {
            $bloodRequest = BloodRequest::withoutGlobalScopes()->where('contact_phone', $donor->phone)->first();
            $bloodRequest ??= new BloodRequest;
            $bloodRequest->contact_phone = $donor->phone;
            $data = BloodRequest::factory()->raw([
                'blood_group_id' => $donor->blood_group_id,
                'district_id' => $donor->district_id,
                'upazila_id' => $donor->upazila_id,
                'union_id' => $donor->union_id,
                'contact_name' => $donor->name,
                'contact_phone' => $donor->phone,
            ]);
            if (array_key_exists('blood_group_id', $data)) {
                $bloodRequest->blood_group_id = $data['blood_group_id'];
            }
            if (array_key_exists('district_id', $data)) {
                $bloodRequest->district_id = $data['district_id'];
            }
            if (array_key_exists('upazila_id', $data)) {
                $bloodRequest->upazila_id = $data['upazila_id'];
            }
            if (array_key_exists('union_id', $data)) {
                $bloodRequest->union_id = $data['union_id'];
            }
            if (array_key_exists('patient_name', $data)) {
                $bloodRequest->patient_name = $data['patient_name'];
            }
            if (array_key_exists('contact_name', $data)) {
                $bloodRequest->contact_name = $data['contact_name'];
            }
            if (array_key_exists('contact_phone', $data)) {
                $bloodRequest->contact_phone = $data['contact_phone'];
            }
            if (array_key_exists('hospital_name', $data)) {
                $bloodRequest->hospital_name = $data['hospital_name'];
            }
            if (array_key_exists('hospital_address', $data)) {
                $bloodRequest->hospital_address = $data['hospital_address'];
            }
            if (array_key_exists('bags_needed', $data)) {
                $bloodRequest->bags_needed = $data['bags_needed'];
            }
            if (array_key_exists('needed_at', $data)) {
                $bloodRequest->needed_at = $data['needed_at'];
            }
            if (array_key_exists('urgency', $data)) {
                $bloodRequest->urgency = $data['urgency'];
            }
            if (array_key_exists('request_status', $data)) {
                $bloodRequest->request_status = $data['request_status'];
            }
            if (array_key_exists('expires_at', $data)) {
                $bloodRequest->expires_at = $data['expires_at'];
            }
            if (array_key_exists('notes', $data)) {
                $bloodRequest->notes = $data['notes'];
            }
            if (array_key_exists('status', $data)) {
                $bloodRequest->status = $data['status'];
            }
            $bloodRequest->save();
        }
    }
}
