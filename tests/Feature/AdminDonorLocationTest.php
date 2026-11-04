<?php

use App\Models\BloodDonor;
use App\Models\BloodGroup;
use App\Models\District;
use App\Models\Union;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(LazilyRefreshDatabase::class);

it('lists active divisions and filters districts by their division', function () {
    $divisionId = DB::table('divisions')->insertGetId(['name' => 'Dhaka']);
    $otherDivisionId = DB::table('divisions')->insertGetId(['name' => 'Sylhet']);
    DB::table('divisions')->insert(['name' => 'Inactive division', 'status' => 0]);
    DB::table('divisions')->insert(['name' => 'Deleted division', 'deleted' => 1]);
    DB::table('divisions')->insert(['name' => 'Soft deleted division', 'deleted_at' => now()]);
    $district = District::factory()->create(['division_id' => $divisionId]);
    District::factory()->create(['division_id' => $otherDivisionId]);
    District::factory()->create(['division_id' => $divisionId, 'status' => 0]);

    $this->getJson(route('locations.index', ['type' => 'divisions']))
        ->assertOk()->assertJsonCount(2, 'data')->assertJsonPath('data.0.id', $divisionId);
    $this->getJson(route('locations.index', ['division_id' => $divisionId]))
        ->assertOk()->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $district->id)
        ->assertJsonPath('data.0.division_id', $divisionId);
    $this->getJson(route('locations.index', ['division_id' => 'invalid']))
        ->assertUnprocessable()->assertJsonValidationErrors('division_id');
});

it('creates donors without an extra checkbox and restores their location for editing', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $union = Union::factory()->create();
    $location = [
        'district_id' => $union->upazila->district_id,
        'upazila_id' => $union->upazila_id,
        'union_id' => $union->id,
    ];

    $response = $this->actingAs($admin)->postJson(route('admin.blood-donors.store'), [
        'name' => 'Test donor',
        'phone' => '01712345678',
        'blood_group_id' => $group->id,
        ...$location,
    ])->assertCreated();

    $donorId = $response->json('data.id');
    $this->assertDatabaseHas('blood_donors', ['id' => $donorId, ...$location]);
    $this->getJson(route('admin.blood-donors.show', $donorId))->assertOk()
        ->assertJsonPath('data.district_id', $location['district_id'])
        ->assertJsonPath('data.upazila_id', $location['upazila_id'])
        ->assertJsonPath('data.union_id', $location['union_id']);
});

it('updates donor location and allows clearing optional locations', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $donor = BloodDonor::factory()->create();
    $union = Union::factory()->create();
    $location = [
        'district_id' => $union->upazila->district_id,
        'upazila_id' => $union->upazila_id,
        'union_id' => $union->id,
    ];

    $this->actingAs($admin)->patchJson(route('admin.blood-donors.update', $donor), $location)->assertOk();

    $this->assertDatabaseHas('blood_donors', ['id' => $donor->id, ...$location]);

    $this->patchJson(route('admin.blood-donors.update', $donor), [
        'district_id' => null,
        'upazila_id' => null,
        'union_id' => null,
    ])->assertOk();

    $this->assertDatabaseHas('blood_donors', [
        'id' => $donor->id,
        'district_id' => null,
        'upazila_id' => null,
        'union_id' => null,
    ]);
});
