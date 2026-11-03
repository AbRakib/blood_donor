<?php

use App\Models\BloodDonor;
use App\Models\BloodGroup;
use App\Models\Union;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('stores donor consent and location and restores them for editing', function () {
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
        'consent_to_contact' => true,
        ...$location,
    ])->assertCreated();

    $donorId = $response->json('data.id');
    $this->assertDatabaseHas('blood_donors', ['id' => $donorId, 'consent_to_contact' => true, ...$location]);
    $this->getJson(route('admin.blood-donors.show', $donorId))->assertOk()
        ->assertJsonPath('data.consent_to_contact', true)
        ->assertJsonPath('data.district_id', $location['district_id'])
        ->assertJsonPath('data.upazila_id', $location['upazila_id'])
        ->assertJsonPath('data.union_id', $location['union_id']);
});

it('updates donor location and allows clearing optional locations and withdrawing consent', function () {
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
        'consent_to_contact' => false,
    ])->assertOk()->assertJsonPath('data.consent_to_contact', false);

    $this->assertDatabaseHas('blood_donors', [
        'id' => $donor->id,
        'district_id' => null,
        'upazila_id' => null,
        'union_id' => null,
        'consent_to_contact' => false,
    ]);
});
