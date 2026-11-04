<?php

use App\Models\BloodDonor;
use App\Models\BloodGroup;
use App\Models\BloodRequest;
use App\Models\Union;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('approves assigns and completes a blood request with verified donation history', function () {
    $this->travelTo(now()->setDate(2026, 11, 4)->startOfDay());
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $request = BloodRequest::factory()->create(['bags_needed' => 1]);
    $donor = BloodDonor::factory()->create(['blood_group_id' => $request->blood_group_id]);

    $this->postJson(route('admin.blood-requests.assign-donor', $request), ['blood_donor_id' => $donor->id])->assertUnprocessable();
    $this->postJson(route('admin.blood-requests.verify', $request))->assertOk()->assertJsonPath('data.request_status', 'verified');
    $this->postJson(route('admin.blood-requests.assign-donor', $request), ['blood_donor_id' => $donor->id])->assertSuccessful();
    expect($request->fresh()->request_status)->toBe('in_progress');

    $this->postJson(route('admin.blood-requests.complete', $request))->assertOk()->assertJsonPath('data.request_status', 'fulfilled');

    $this->assertDatabaseHas('blood_donations', ['blood_request_id' => $request->id, 'blood_donor_id' => $donor->id, 'bags' => 1, 'donation_status' => 'verified']);
    $this->assertDatabaseHas('request_donors', ['blood_request_id' => $request->id, 'blood_donor_id' => $donor->id, 'response_status' => 'donated']);
    expect($donor->fresh()->last_donation_date?->toDateString())->toBe('2026-11-04');
    expect($donor->fresh()->total_donations)->toBe(1)
        ->and($donor->fresh()->is_available)->toBeFalse();

    $this->postJson(route('admin.blood-requests.complete', $request))->assertUnprocessable();
    $this->assertDatabaseCount('blood_donations', 1);
});

it('rejects completion outside the assigned stage', function (string $status) {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $request = BloodRequest::factory()->create(['request_status' => $status]);

    $this->postJson(route('admin.blood-requests.complete', $request))->assertUnprocessable();
    $this->assertDatabaseCount('blood_donations', 0);
})->with(['pending', 'verified', 'cancelled', 'expired', 'fulfilled']);

it('requires enough assigned donors before recording any donations', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $request = BloodRequest::factory()->verified()->create(['bags_needed' => 2]);
    $donor = BloodDonor::factory()->create(['blood_group_id' => $request->blood_group_id]);
    $request->assignDonor($donor);

    $this->postJson(route('admin.blood-requests.complete', $request))->assertUnprocessable()->assertJsonValidationErrors('blood_donor_id');
    $this->assertDatabaseCount('blood_donations', 0);
    expect($request->fresh()->request_status)->toBe('in_progress');
});

it('restricts request completion to admins', function () {
    $request = BloodRequest::factory()->create();
    $this->postJson(route('admin.blood-requests.complete', $request))->assertUnauthorized();
    $this->actingAs(User::factory()->create())->postJson(route('admin.blood-requests.complete', $request))->assertForbidden();
});

it('assigns a matching donor from a different location', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $requestUnion = Union::factory()->create();
    $donorUnion = Union::factory()->create();
    $request = BloodRequest::factory()->verified()->create([
        'district_id' => $requestUnion->upazila->district_id,
        'upazila_id' => $requestUnion->upazila_id,
        'union_id' => $requestUnion->id,
    ]);
    $donor = BloodDonor::factory()->create([
        'blood_group_id' => $request->blood_group_id,
        'district_id' => $donorUnion->upazila->district_id,
        'upazila_id' => $donorUnion->upazila_id,
        'union_id' => $donorUnion->id,
    ]);

    $this->postJson(route('admin.blood-requests.assign-donor', $request), ['blood_donor_id' => $donor->id])->assertSuccessful();

    $this->assertDatabaseHas('request_donors', ['blood_request_id' => $request->id, 'blood_donor_id' => $donor->id]);
    $this->assertDatabaseHas('blood_requests', ['id' => $request->id, 'request_status' => 'in_progress']);
});

it('accepts today without a time and keeps the request open through its expiry date', function () {
    $this->travelTo(now()->setTime(14, 30));
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $group = BloodGroup::factory()->create();
    $date = today()->toDateString();

    $response = $this->postJson(route('admin.blood-requests.store'), [
        'blood_group_id' => $group->id,
        'patient_name' => 'Test Patient',
        'contact_name' => 'Test Contact',
        'contact_phone' => '01712345678',
        'hospital_name' => 'Manda Hospital',
        'bags_needed' => 1,
        'needed_at' => $date,
        'expires_at' => $date,
    ])->assertCreated();

    $this->assertDatabaseHas('blood_requests', [
        'id' => $response->json('data.id'),
        'needed_at' => $date.' 00:00:00',
        'expires_at' => $date.' 23:59:59',
    ]);
});
