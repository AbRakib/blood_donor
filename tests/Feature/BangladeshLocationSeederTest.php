<?php

use App\Models\BloodDonor;
use App\Models\District;
use App\Models\Union;
use App\Models\Upazila;
use Database\Seeders\BangladeshLocationSeeder;
use Database\Seeders\DivisionSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(LazilyRefreshDatabase::class);

it('seeds the nationwide hierarchy without duplicates on subsequent runs', function () {
    $this->seed(BangladeshLocationSeeder::class);
    $this->seed(BangladeshLocationSeeder::class);

    $this->assertDatabaseCount('divisions', 8);
    $this->assertDatabaseCount('districts', 64);
    $this->assertDatabaseCount('upazilas', 494);
    $this->assertDatabaseCount('unions', 4537);

    $union = Union::where('name', 'Prasadpur')->whereHas('upazila', fn ($query) => $query->where('name', 'Manda'))->firstOrFail();
    expect($union->name_bn)->toBe('প্রসাদপুর');
    expect($union->upazila->district->name)->toBe('Naogaon');
    $this->assertDatabaseHas('divisions', ['id' => $union->upazila->district->division_id, 'name' => 'Rajshahi', 'name_bn' => 'রাজশাহী']);
    $this->assertDatabaseHas('unions', ['name' => 'Natai North', 'name_bn' => 'নাটাই (উত্তর)']);
    $this->assertDatabaseHas('unions', ['name' => 'Natai South', 'name_bn' => 'নাটাই (দক্ষিন)']);

    expect(DB::table('districts')->whereNull('division_id')->count())->toBe(0);
    expect(DB::table('upazilas')->leftJoin('districts', 'districts.id', '=', 'upazilas.district_id')->whereNull('districts.id')->count())->toBe(0);
    expect(DB::table('unions')->leftJoin('upazilas', 'upazilas.id', '=', 'unions.upazila_id')->whereNull('upazilas.id')->count())->toBe(0);
});

it('preserves existing location ids codes and donor relationships', function () {
    $district = District::factory()->create(['name' => 'Naogaon', 'name_bn' => 'নওগাঁ', 'code' => '64']);
    $upazila = Upazila::factory()->create(['district_id' => $district->id, 'name' => 'Manda', 'name_bn' => 'মান্দা']);
    $union = Union::factory()->create(['upazila_id' => $upazila->id, 'name' => 'Prasadpur', 'name_bn' => 'প্রসাদপুর']);
    $donor = BloodDonor::factory()->create(['district_id' => $district->id, 'upazila_id' => $upazila->id, 'union_id' => $union->id]);

    $this->seed(BangladeshLocationSeeder::class);

    $this->assertDatabaseCount('districts', 64);
    $this->assertDatabaseCount('upazilas', 494);
    $this->assertDatabaseCount('unions', 4537);
    $this->assertDatabaseHas('districts', ['id' => $district->id, 'name' => 'Naogaon', 'code' => '64']);
    $this->assertDatabaseHas('upazilas', ['id' => $upazila->id, 'district_id' => $district->id, 'name' => 'Manda']);
    $this->assertDatabaseHas('unions', ['id' => $union->id, 'upazila_id' => $upazila->id, 'name' => 'Prasadpur']);
    $this->assertDatabaseHas('blood_donors', ['id' => $donor->id, 'district_id' => $district->id, 'upazila_id' => $upazila->id, 'union_id' => $union->id]);
});

it('seeds divisions independently without importing child locations', function () {
    $this->seed(DivisionSeeder::class);

    $this->assertDatabaseCount('divisions', 8);
    $this->assertDatabaseCount('districts', 0);
});
