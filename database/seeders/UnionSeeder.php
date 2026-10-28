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
            $union = Union::withoutGlobalScopes()->where('upazila_id', $upazila->id)->where('name', $name)->first();
            $union ??= new Union;
            $union->upazila_id = $upazila->id;
            $union->name = $name;
            $union->name_bn = $nameBn;
            $union->save();
        }
    }
}
