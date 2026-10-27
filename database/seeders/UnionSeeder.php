<?php

namespace Database\Seeders;

use App\Models\Union;
use App\Models\Upazila;
use Illuminate\Database\Seeder;

class UnionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UpazilaSeeder::class);
        $upazila = Upazila::where('name', 'Manda')->whereHas('district', fn ($query) => $query->where('code', '64'))->firstOrFail();
        foreach (['Prasadpur' => 'প্রসাদপুর', 'Kusumba' => 'কুসুম্বা'] as $name => $nameBn) {
            Union::withoutGlobalScopes()->firstOrCreate(['upazila_id' => $upazila->id, 'name' => $name], ['name_bn' => $nameBn]);
        }
    }
}
