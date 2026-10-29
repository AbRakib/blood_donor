<?php

use App\Models\BloodDonation;
use App\Models\BloodDonor;
use App\Models\BloodGroup;
use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('fulfills a request only after every required bag has been verified', function () {
    $this->freezeTime();
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $request = BloodRequest::factory()->verified()->create(['blood_group_id' => $group->id, 'bags_needed' => 2]);
    $donors = BloodDonor::factory()->count(2)->create(['blood_group_id' => $group->id]);
    $first = BloodDonation::factory()->create(['blood_request_id' => $request->id, 'blood_donor_id' => $donors[0]->id, 'bags' => 1]);
    $second = BloodDonation::factory()->create(['blood_request_id' => $request->id, 'blood_donor_id' => $donors[1]->id, 'bags' => 1]);

    $this->actingAs($admin)->postJson(route('admin.donations.verify', $first))->assertOk();
    $this->assertDatabaseHas('blood_requests', ['id' => $request->id, 'request_status' => 'in_progress']);
    $this->postJson(route('admin.donations.verify', $second))->assertOk();
    $this->postJson(route('admin.donations.verify', $second))->assertOk();

    $this->assertDatabaseHas('blood_requests', ['id' => $request->id, 'request_status' => 'fulfilled']);
    $this->assertDatabaseCount('blood_request_status_logs', 2);
    $this->assertDatabaseCount('donor_availability_logs', 2);
    expect($donors[0]->fresh())->total_donations->toBe(1);
    expect($donors[1]->fresh())->total_donations->toBe(1);
});

it('logs availability changes once while preserving unrelated donor details', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $donor = BloodDonor::factory()->create(['is_available' => true]);
    $originalPhone = $donor->phone;

    $this->actingAs($admin)->patchJson(route('admin.blood-donors.update', $donor), ['is_available' => false])->assertOk();
    $this->patchJson(route('admin.blood-donors.update', $donor), ['name' => 'Updated donor'])->assertOk();
    $this->patchJson(route('admin.blood-donors.update', $donor), ['is_available' => false])->assertOk();

    $this->assertDatabaseHas('blood_donors', ['id' => $donor->id, 'name' => 'Updated donor', 'phone' => $originalPhone, 'is_available' => false]);
    $this->assertDatabaseHas('donor_availability_logs', ['blood_donor_id' => $donor->id, 'was_available' => true, 'is_available' => false]);
    $this->assertDatabaseCount('donor_availability_logs', 1);
});
