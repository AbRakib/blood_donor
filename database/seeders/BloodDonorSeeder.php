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

            BloodDonor::withoutGlobalScopes()->updateOrCreate(
                ['phone' => '017000000'.str_pad((string) $index, 2, '0', STR_PAD_LEFT)],
                BloodDonor::factory()->raw([
                    'phone' => '017000000'.str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                    'blood_group_id' => $bloodGroup->id,
                    'district_id' => $union->upazila->district_id,
                    'upazila_id' => $union->upazila_id,
                    'union_id' => $union->id,
                    'area' => $union->name,
                    'address' => $union->name.', Manda, Naogaon, Bangladesh',
                ]),
            );
        }
    }
}
