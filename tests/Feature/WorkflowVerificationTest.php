<?php

use App\Models\BloodDonation;
use App\Models\BloodDonor;
use App\Models\BloodGroup;
use App\Models\BloodRequest;
use App\Models\RequestDonor;
use App\Models\Upazila;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('leaves donor totals and request fulfillment unchanged until a donation is verified', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $donor = BloodDonor::factory()->create(['blood_group_id' => $group->id]);
    $request = BloodRequest::factory()->verified()->create(['blood_group_id' => $group->id]);

    $response = $this->actingAs($admin)->postJson(route('api.v1.admin.donations.store'), [
        'blood_donor_id' => $donor->id, 'blood_request_id' => $request->id,
        'bags' => 1, 'donated_at' => today()->toDateString(), 'donation_status' => 'verified', 'verified_by' => $admin->id,
    ])->assertCreated()->assertJsonPath('data.donation_status', 'pending');

    expect($donor->fresh())->total_donations->toBe(0)->last_donation_date->toBeNull()->is_available->toBeTrue();
    $this->assertDatabaseHas('blood_requests', ['id' => $request->id, 'request_status' => 'in_progress']);
    $this->assertDatabaseHas('blood_donations', ['id' => $response->json('data.id'), 'verified_at' => null, 'verified_by' => null]);

    $this->postJson(route('api.v1.admin.donations.verify', $response->json('data.id')))->assertOk()->assertJsonPath('data.donation_status', 'verified');

    expect($donor->fresh())->total_donations->toBe(1)->last_donation_date->toDateString()->toBe(today()->toDateString());
    $this->assertDatabaseHas('blood_donations', ['id' => $response->json('data.id'), 'verified_by' => $admin->id]);
    $this->assertDatabaseHas('blood_requests', ['id' => $request->id, 'request_status' => 'fulfilled']);
});

it('records actual donations independently of contact availability and consent', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $donor = BloodDonor::factory()->create(['is_available' => false, 'consent_to_contact' => false]);

    $response = $this->actingAs($admin)->postJson(route('api.v1.admin.donations.store'), [
        'blood_donor_id' => $donor->id, 'bags' => 1, 'donated_at' => today()->toDateString(),
    ])->assertCreated();
    $this->postJson(route('api.v1.admin.donations.verify', $response->json('data.id')))->assertOk();

    expect($donor->fresh())->total_donations->toBe(1)->is_available->toBeFalse();
    $this->assertDatabaseCount('donor_availability_logs', 0);
});

it('verifies donations idempotently and preserves the latest date when older history is confirmed', function () {
    $this->travelTo(now()->setDate(2026, 10, 27));
    $admin = User::factory()->create(['is_admin' => true]);
    $donor = BloodDonor::factory()->create();
    $latest = BloodDonation::factory()->create(['blood_donor_id' => $donor->id, 'donated_at' => '2026-10-27']);
    $earlier = BloodDonation::factory()->create(['blood_donor_id' => $donor->id, 'donated_at' => '2026-01-01']);

    $this->actingAs($admin)->postJson(route('api.v1.admin.donations.verify', $latest))->assertOk();
    $this->postJson(route('api.v1.admin.donations.verify', $latest))->assertOk();
    $this->postJson(route('api.v1.admin.donations.verify', $earlier))->assertOk();

    expect($donor->fresh())->total_donations->toBe(2)->last_donation_date->toDateString()->toBe('2026-10-27');
    $this->assertDatabaseCount('donor_availability_logs', 1);
});

it('rejects fulfillment while the requested units remain unverified', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $donor = BloodDonor::factory()->create(['blood_group_id' => $group->id]);
    $request = BloodRequest::factory()->verified()->create(['blood_group_id' => $group->id, 'request_status' => 'in_progress']);
    BloodDonation::factory()->create(['blood_donor_id' => $donor->id, 'blood_request_id' => $request->id]);

    $this->actingAs($admin)->patchJson(route('api.v1.admin.blood-requests.status', $request), ['request_status' => 'fulfilled'])
        ->assertUnprocessable()->assertJsonValidationErrors('request_status');

    $this->assertDatabaseHas('blood_requests', ['id' => $request->id, 'request_status' => 'in_progress']);
    $this->assertDatabaseCount('blood_request_status_logs', 0);
});

it('does not verify another pending donation after the requested units have been fulfilled', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $first = BloodDonor::factory()->create(['blood_group_id' => $group->id]);
    $second = BloodDonor::factory()->create(['blood_group_id' => $group->id]);
    $request = BloodRequest::factory()->verified()->create(['blood_group_id' => $group->id]);
    $firstDonation = BloodDonation::factory()->create(['blood_donor_id' => $first->id, 'blood_request_id' => $request->id]);
    $secondDonation = BloodDonation::factory()->create(['blood_donor_id' => $second->id, 'blood_request_id' => $request->id]);

    $this->actingAs($admin)->postJson(route('api.v1.admin.donations.verify', $firstDonation))->assertOk();
    $this->postJson(route('api.v1.admin.donations.verify', $secondDonation))->assertUnprocessable();

    $this->assertDatabaseHas('blood_donations', ['id' => $secondDonation->id, 'donation_status' => 'pending']);
    expect($second->fresh())->total_donations->toBe(0)->last_donation_date->toBeNull();
});

it('requires request verification before donor assignment and records the full workflow', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $donor = BloodDonor::factory()->create(['blood_group_id' => $group->id]);
    $request = BloodRequest::factory()->create(['blood_group_id' => $group->id]);

    $this->actingAs($admin)->postJson(route('api.v1.admin.blood-requests.assign-donor', $request), ['blood_donor_id' => $donor->id])->assertUnprocessable();
    $this->postJson(route('api.v1.admin.blood-requests.verify', $request))->assertOk()->assertJsonPath('data.request_status', 'verified');
    $this->postJson(route('api.v1.admin.blood-requests.assign-donor', $request), ['blood_donor_id' => $donor->id])->assertCreated();
    $this->postJson(route('api.v1.admin.blood-requests.assign-donor', $request), ['blood_donor_id' => $donor->id])->assertOk();

    $this->assertDatabaseCount('request_donors', 1);
    $this->assertDatabaseHas('blood_requests', ['id' => $request->id, 'request_status' => 'in_progress', 'verified_by' => $admin->id]);
    $this->assertDatabaseHas('blood_request_status_logs', ['blood_request_id' => $request->id, 'from_status' => 'pending', 'to_status' => 'verified']);
    $this->assertDatabaseHas('blood_request_status_logs', ['blood_request_id' => $request->id, 'from_status' => 'verified', 'to_status' => 'in_progress']);
});

it('rejects assigning donors who are not contactable', function (array $attributes) {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $donor = BloodDonor::factory()->create(array_merge(['blood_group_id' => $group->id], $attributes));
    $request = BloodRequest::factory()->verified()->create(['blood_group_id' => $group->id]);

    $this->actingAs($admin)->postJson(route('api.v1.admin.blood-requests.assign-donor', $request), ['blood_donor_id' => $donor->id])->assertUnprocessable();

    $this->assertDatabaseCount('request_donors', 0);
    $this->assertDatabaseHas('blood_requests', ['id' => $request->id, 'request_status' => 'verified']);
})->with([
    'inactive' => [['status' => 0]],
    'unavailable' => [['is_available' => false]],
    'no consent' => [['consent_to_contact' => false]],
]);

it('accepts critical urgency and includes it among open urgent dashboard requests', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();

    $this->actingAs($admin)->postJson(route('api.v1.admin.blood-requests.store'), [
        'blood_group_id' => $group->id, 'patient_name' => 'Patient', 'contact_name' => 'Contact',
        'contact_phone' => '01712345678', 'hospital_name' => 'Hospital', 'bags_needed' => 1,
        'needed_at' => now()->addDay()->toIso8601String(), 'urgency' => 'critical',
    ])->assertCreated()->assertJsonPath('data.urgency', 'critical');

    $this->getJson(route('api.v1.admin.dashboard.statistics'))->assertOk()->assertJsonPath('data.pending_requests', 1)->assertJsonPath('data.urgent_requests', 1);
    $this->getJson(route('api.v1.admin.dashboard.recent-requests'))->assertOk()->assertJsonCount(1, 'data');
});

it('expires only due open requests and records one history entry across repeated runs', function () {
    $this->freezeTime();
    $group = BloodGroup::factory()->create();
    $due = BloodRequest::factory()->verified()->create(['blood_group_id' => $group->id, 'expires_at' => now()->subMinute()]);
    $future = BloodRequest::factory()->create(['blood_group_id' => $group->id, 'expires_at' => now()->addDay()]);
    $cancelled = BloodRequest::factory()->create(['blood_group_id' => $group->id, 'request_status' => 'cancelled', 'expires_at' => now()->subMinute()]);

    $this->artisan('blood-requests:expire')->assertSuccessful();
    $this->artisan('blood-requests:expire')->assertSuccessful();

    $this->assertDatabaseHas('blood_requests', ['id' => $due->id, 'request_status' => 'expired']);
    $this->assertDatabaseHas('blood_requests', ['id' => $future->id, 'request_status' => 'pending']);
    $this->assertDatabaseHas('blood_requests', ['id' => $cancelled->id, 'request_status' => 'cancelled']);
    $this->assertDatabaseCount('blood_request_status_logs', 1);
});

it('prevents verification and matching after the request expiry time', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $request = BloodRequest::factory()->create(['expires_at' => now()->subMinute()]);

    $this->actingAs($admin)->postJson(route('api.v1.admin.blood-requests.verify', $request))->assertUnprocessable();
    $this->postJson(route('api.v1.admin.blood-requests.matches', $request))->assertUnprocessable();

    $this->assertDatabaseCount('request_donors', 0);
    $this->assertDatabaseCount('blood_request_status_logs', 0);
});

it('rejects premature expiration and reopening terminal requests', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $request = BloodRequest::factory()->create(['expires_at' => now()->addDay()]);

    $this->actingAs($admin)->patchJson(route('api.v1.admin.blood-requests.status', $request), ['request_status' => 'expired'])->assertUnprocessable();
    $this->patchJson(route('api.v1.admin.blood-requests.status', $request), ['request_status' => 'cancelled'])->assertOk();
    $this->postJson(route('api.v1.admin.blood-requests.verify', $request))->assertUnprocessable();

    $this->assertDatabaseHas('blood_requests', ['id' => $request->id, 'request_status' => 'cancelled']);
});

it('does not allow donor registration or edits to inject verified history', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $donor = BloodDonor::factory()->create();

    $this->actingAs($admin)->patchJson(route('api.v1.admin.blood-donors.update', $donor), ['last_donation_date' => today()->toDateString(), 'total_donations' => 99])
        ->assertUnprocessable()->assertJsonValidationErrors(['last_donation_date', 'total_donations']);

    expect($donor->fresh())->total_donations->toBe(0)->last_donation_date->toBeNull();
});

it('redacts mobile numbers when donor consent has been withdrawn including nested matches', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $donor = BloodDonor::factory()->create(['blood_group_id' => $group->id, 'consent_to_contact' => false]);
    $request = BloodRequest::factory()->verified()->create(['blood_group_id' => $group->id]);
    RequestDonor::factory()->create(['blood_donor_id' => $donor->id, 'blood_request_id' => $request->id]);

    $this->actingAs($admin)->getJson(route('api.v1.admin.blood-donors.show', $donor))->assertOk()->assertJsonPath('data.phone', null)->assertDontSee($donor->phone);
    $this->getJson(route('api.v1.admin.blood-requests.show', $request))->assertOk()->assertJsonPath('data.request_donors.0.blood_donor.phone', null)->assertDontSee($donor->phone);
});

it('excludes pending donations from history and reports and excludes inactive or deleted donors from totals', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $donor = BloodDonor::factory()->create(['blood_group_id' => $group->id, 'consent_to_contact' => false]);
    BloodDonor::factory()->create(['blood_group_id' => $group->id, 'status' => 0]);
    BloodDonor::factory()->create(['blood_group_id' => $group->id, 'deleted' => 1]);
    BloodDonation::factory()->create(['blood_donor_id' => $donor->id]);

    $this->actingAs($admin)->getJson(route('api.v1.admin.blood-donors.history', $donor))->assertOk()->assertJsonCount(0, 'data');
    $this->getJson(route('api.v1.admin.dashboard'))->assertOk()->assertJsonPath('data.donors', 1)->assertJsonPath('data.available_donors', 1)->assertJsonPath('data.donations', 0)->assertJsonPath('data.bags_donated', 0);
});

it('returns 403 when nonadministrators attempt donation verification', function () {
    $user = User::factory()->create();
    $donation = BloodDonation::factory()->create();

    $this->actingAs($user)->postJson(route('api.v1.admin.donations.verify', $donation))->assertForbidden();

    $this->assertDatabaseHas('blood_donations', ['id' => $donation->id, 'donation_status' => 'pending']);
});

it('preserves valid saved location relationships when updating other profile or request fields', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $upazila = Upazila::factory()->create();
    $location = ['district_id' => $upazila->district_id, 'upazila_id' => $upazila->id];
    $donor = BloodDonor::factory()->create(array_merge($location, ['blood_group_id' => $group->id]));
    $request = BloodRequest::factory()->create(array_merge($location, ['blood_group_id' => $group->id]));

    $this->actingAs($admin)->patchJson(route('api.v1.admin.blood-donors.update', $donor), ['name' => 'Updated name'])->assertOk();
    $this->patchJson(route('api.v1.admin.blood-requests.update', $request), ['request_status' => 'verified'])->assertOk();

    $this->assertDatabaseHas('blood_donors', array_merge(['id' => $donor->id, 'name' => 'Updated name'], $location));
    $this->assertDatabaseHas('blood_requests', array_merge(['id' => $request->id, 'request_status' => 'verified'], $location));
});
