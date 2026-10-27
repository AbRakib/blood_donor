<?php

namespace Database\Seeders;

use App\Models\BloodGroup;
use Illuminate\Database\Seeder;

class BloodGroupSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $order => $name) {
            BloodGroup::withoutGlobalScopes()->updateOrCreate(['name' => $name], ['sort_order' => $order]);
        }
    }
}
