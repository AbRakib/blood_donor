<?php

namespace Database\Seeders;

use App\Models\BloodGroup;
use Illuminate\Database\Seeder;

class BloodGroupSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $order => $name) {
            $bloodGroup = BloodGroup::withoutGlobalScopes()->where('name', $name)->first();
            $bloodGroup ??= new BloodGroup;
            $bloodGroup->name = $name;
            $bloodGroup->sort_order = $order;
            $bloodGroup->save();
        }
    }
}
