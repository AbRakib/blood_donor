<?php

namespace Database\Seeders;

use App\Models\BloodDonor;
use App\Models\BloodGroup;
use App\Models\Union;
use Illuminate\Database\Seeder;

class BloodDonorSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BloodGroupSeeder::class);
        $this->call(UnionSeeder::class);

        $unions = Union::with('upazila')->whereHas('upazila', fn ($query) => $query->where('name', 'Manda')->whereHas('district', fn ($query) => $query->where('code', '64')))->orderBy('id')->get();

        foreach (BloodGroup::orderBy('sort_order')->get() as $index => $bloodGroup) {
            $union = $unions[$index % $unions->count()];

            $bloodDonor = BloodDonor::withoutGlobalScopes()->where('phone', '017000000'.str_pad((string) $index, 2, '0', STR_PAD_LEFT))->first();
            $bloodDonor ??= new BloodDonor;
            $bloodDonor->phone = '017000000'.str_pad((string) $index, 2, '0', STR_PAD_LEFT);
            $data = BloodDonor::factory()->raw([
                'phone' => '017000000'.str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                'blood_group_id' => $bloodGroup->id,
                'district_id' => $union->upazila->district_id,
                'upazila_id' => $union->upazila_id,
                'union_id' => $union->id,
                'area' => $union->name,
                'address' => $union->name.', Manda, Naogaon, Bangladesh',
            ]);
            if (array_key_exists('name', $data)) {
                $bloodDonor->name = $data['name'];
            }
            if (array_key_exists('blood_group_id', $data)) {
                $bloodDonor->blood_group_id = $data['blood_group_id'];
            }
            if (array_key_exists('phone', $data)) {
                $bloodDonor->phone = $data['phone'];
            }
            if (array_key_exists('area', $data)) {
                $bloodDonor->area = $data['area'];
            }
            if (array_key_exists('address', $data)) {
                $bloodDonor->address = $data['address'];
            }
            if (array_key_exists('is_available', $data)) {
                $bloodDonor->is_available = $data['is_available'];
            }
            if (array_key_exists('district_id', $data)) {
                $bloodDonor->district_id = $data['district_id'];
            }
            if (array_key_exists('upazila_id', $data)) {
                $bloodDonor->upazila_id = $data['upazila_id'];
            }
            if (array_key_exists('union_id', $data)) {
                $bloodDonor->union_id = $data['union_id'];
            }
            if (array_key_exists('date_of_birth', $data)) {
                $bloodDonor->date_of_birth = $data['date_of_birth'];
            }
            if (array_key_exists('gender', $data)) {
                $bloodDonor->gender = $data['gender'];
            }
            if (array_key_exists('consent_to_contact', $data)) {
                $bloodDonor->consent_to_contact = $data['consent_to_contact'];
            }
            if (array_key_exists('status', $data)) {
                $bloodDonor->status = $data['status'];
            }
            if (array_key_exists('last_donation_date', $data)) {
                $bloodDonor->last_donation_date = $data['last_donation_date'];
            }
            $bloodDonor->save();
        }
    }
}
