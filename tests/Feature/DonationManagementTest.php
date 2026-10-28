<?php

use App\Models\BloodDonation;
use App\Models\BloodDonor;
use App\Models\BloodGroup;
use App\Models\BloodRequest;
use App\Models\District;
use App\Models\DonorContactRequest;
use App\Models\RequestDonor;
use App\Models\SystemSetting;
use App\Models\Upazila;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Gate;

uses(LazilyRefreshDatabase::class);

it('seeds eight groups and locations idempotently without creating an admin password', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    $this->assertDatabaseCount('blood_groups', 8);
    $this->assertDatabaseCount('districts', 1);
    $this->assertDatabaseCount('upazilas', 1);
    $this->assertDatabaseCount('unions', 2);
    $this->assertDatabaseCount('system_settings', 2);
    $this->assertDatabaseCount('users', 0);
});

it('exposes only consenting active donors and keeps their contact details private', function () {
    $group = BloodGroup::factory()->create();
    $donor = BloodDonor::factory()->create(['blood_group_id' => $group->id]);
    BloodDonor::factory()->create(['blood_group_id' => $group->id, 'consent_to_contact' => false]);
    BloodDonor::factory()->create(['blood_group_id' => $group->id, 'status' => 0]);
    BloodDonor::factory()->create(['blood_group_id' => $group->id, 'is_available' => false]);
    BloodDonor::factory()->create(['blood_group_id' => $group->id, 'deleted' => 1]);

    $this->getJson(route('donors.index'))->assertOk()->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $donor->id)->assertJsonMissingPath('data.0.phone')
        ->assertJsonMissingPath('data.0.address')->assertJsonMissingPath('data.0.date_of_birth');
});

it('stores public registrations for admin review and ignores injected administrative fields', function () {
    $group = BloodGroup::factory()->create();

    $this->postJson(route('donors.store'), [
        'name' => 'New donor', 'phone' => '01712345678', 'blood_group_id' => $group->id,
        'consent_to_contact' => true, 'status' => 1, 'created_by' => 99, 'deleted' => 1,
    ])->assertCreated();

    $this->assertDatabaseHas('blood_donors', ['name' => 'New donor', 'status' => 0, 'deleted' => 0, 'created_by' => null]);
});

it('rejects registration without consent or a valid Bangladesh phone', function () {
    $group = BloodGroup::factory()->create();

    $this->postJson(route('donors.store'), ['name' => 'Donor', 'phone' => '123', 'blood_group_id' => $group->id, 'consent_to_contact' => false])
        ->assertUnprocessable()->assertJsonValidationErrors(['phone', 'consent_to_contact']);

    $this->assertDatabaseCount('blood_donors', 0);
});

it('rejects inconsistent location hierarchies', function () {
    $group = BloodGroup::factory()->create();
    $district = District::factory()->create();
    $upazila = Upazila::factory()->create();

    $this->postJson(route('donors.store'), ['name' => 'Donor', 'phone' => '01712345678', 'blood_group_id' => $group->id,
        'district_id' => $district->id, 'upazila_id' => $upazila->id, 'consent_to_contact' => true])
        ->assertUnprocessable()->assertJsonValidationErrors('upazila_id');

    $this->assertDatabaseCount('blood_donors', 0);
});

it('returns 401 for unauthenticated admin requests', function () {
    $this->getJson(route('admin.dashboard'))->assertUnauthorized();
});

it('returns 403 for authenticated users without admin privileges', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->getJson(route('admin.dashboard'))->assertForbidden();
});

it('enforces the donor and request policy permissions', function (bool $isAdmin) {
    $user = User::factory()->make(['is_admin' => $isAdmin]);
    $donor = BloodDonor::factory()->make(['blood_group_id' => 1]);
    $request = BloodRequest::factory()->make(['blood_group_id' => 1]);

    foreach (['viewAny', 'create'] as $ability) {
        expect(Gate::forUser($user)->allows($ability, BloodDonor::class))->toBe($isAdmin);
        expect(Gate::forUser($user)->allows($ability, BloodRequest::class))->toBe($isAdmin);
    }
    foreach (['view', 'update', 'delete'] as $ability) {
        expect(Gate::forUser($user)->allows($ability, $donor))->toBe($isAdmin);
        expect(Gate::forUser($user)->allows($ability, $request))->toBe($isAdmin);
    }
})->with([true, false]);

it('logs availability changes and soft deletion with the acting administrator', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $donor = BloodDonor::factory()->create();

    $this->actingAs($admin)->patchJson(route('admin.blood-donors.update', $donor), ['is_available' => false])->assertOk();

    $this->assertDatabaseHas('donor_availability_logs', ['blood_donor_id' => $donor->id, 'was_available' => 1, 'is_available' => 0, 'created_by' => $admin->id]);
    $this->assertDatabaseCount('activity_logs', 0);

    $this->deleteJson(route('admin.blood-donors.destroy', $donor))->assertNoContent();

    $this->assertDatabaseCount('activity_logs', 0);
    $this->assertSoftDeleted($donor);
    $this->assertDatabaseHas('blood_donors', ['id' => $donor->id, 'deleted' => 1, 'deleted_by' => $admin->id]);
    $this->getJson(route('admin.blood-donors.show', $donor))->assertNotFound();
});

it('matches available consenting donors by blood group and location without duplicates', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $district = District::factory()->create();
    $request = BloodRequest::factory()->verified()->create(['blood_group_id' => $group->id, 'district_id' => $district->id]);
    $donor = BloodDonor::factory()->create(['blood_group_id' => $group->id, 'district_id' => $district->id]);
    BloodDonor::factory()->create(['blood_group_id' => $group->id, 'district_id' => $district->id, 'is_available' => false]);
    BloodDonor::factory()->create(['blood_group_id' => $group->id]);

    $this->actingAs($admin)->postJson(route('admin.blood-requests.matches', $request))->assertOk()->assertJsonCount(1, 'data');
    $this->postJson(route('admin.blood-requests.matches', $request))->assertOk()->assertJsonCount(1, 'data');

    $this->assertDatabaseHas('request_donors', ['blood_request_id' => $request->id, 'blood_donor_id' => $donor->id]);
    $this->assertDatabaseCount('request_donors', 1);
    $this->assertDatabaseHas('blood_request_status_logs', ['blood_request_id' => $request->id, 'from_status' => 'verified', 'to_status' => 'in_progress']);
});

it('records a donation atomically and fulfills the request with history', function () {
    $this->travelTo(now()->setDate(2026, 10, 27)->startOfDay());
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $donor = BloodDonor::factory()->create(['blood_group_id' => $group->id]);
    $request = BloodRequest::factory()->verified()->create(['blood_group_id' => $group->id]);

    $response = $this->actingAs($admin)->postJson(route('admin.donations.store'), ['blood_donor_id' => $donor->id,
        'blood_request_id' => $request->id, 'donated_at' => '2026-10-27', 'bags' => 1])->assertCreated();
    $this->postJson(route('admin.donations.verify', $response->json('data.id')))->assertOk();

    $this->assertDatabaseHas('blood_donations', ['blood_donor_id' => $donor->id, 'blood_request_id' => $request->id, 'bags' => 1]);
    expect($donor->fresh())->is_available->toBeFalse()->last_donation_date->toDateString()->toBe('2026-10-27');
    $this->assertDatabaseHas('blood_requests', ['id' => $request->id, 'request_status' => 'fulfilled']);
    $this->assertDatabaseHas('request_donors', ['blood_donor_id' => $donor->id, 'blood_request_id' => $request->id, 'response_status' => 'donated']);
    $this->assertDatabaseHas('blood_request_status_logs', ['blood_request_id' => $request->id, 'to_status' => 'fulfilled']);
    $this->assertDatabaseHas('donor_availability_logs', ['blood_donor_id' => $donor->id, 'is_available' => 0]);
});

it('rejects donations for incompatible blood groups without changing history', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create(['name' => 'A+']);
    $other = BloodGroup::factory()->create(['name' => 'B+']);
    $donor = BloodDonor::factory()->create(['blood_group_id' => $group->id]);
    $request = BloodRequest::factory()->verified()->create(['blood_group_id' => $other->id]);

    $this->actingAs($admin)->postJson(route('admin.donations.store'), ['blood_donor_id' => $donor->id,
        'blood_request_id' => $request->id, 'donated_at' => today()->toDateString(), 'bags' => 1])
        ->assertUnprocessable()->assertJsonValidationErrors('blood_request_id');

    $this->assertDatabaseCount('blood_donations', 0);
    $this->assertDatabaseCount('donor_availability_logs', 0);
    expect($donor->fresh()->is_available)->toBeTrue();
});

it('stores contact requests without disclosing the donor phone', function () {
    $donor = BloodDonor::factory()->create();

    $this->postJson(route('contact-requests.store'), ['blood_donor_id' => $donor->id,
        'requester_name' => 'Requester', 'requester_phone' => '01812345678', 'message' => 'Please help.'])
        ->assertCreated()->assertJsonMissingPath('data')->assertDontSee($donor->phone);

    $this->assertDatabaseHas('donor_contact_requests', ['blood_donor_id' => $donor->id, 'contact_status' => 'pending']);
});

it('rejects contact requests for nonconsenting donors', function () {
    $donor = BloodDonor::factory()->create(['consent_to_contact' => false]);

    $this->postJson(route('contact-requests.store'), ['blood_donor_id' => $donor->id,
        'requester_name' => 'Requester', 'requester_phone' => '01812345678', 'message' => 'Please help.'])
        ->assertUnprocessable()->assertJsonValidationErrors('blood_donor_id');

    $this->assertDatabaseCount('donor_contact_requests', 0);
});

it('allows admins to approve contact requests and prevents reopening completed requests', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $contact = DonorContactRequest::factory()->create();

    $this->actingAs($admin)->patchJson(route('admin.contact-requests.update', $contact), ['contact_status' => 'approved'])->assertOk();
    $this->patchJson(route('admin.contact-requests.update', $contact), ['contact_status' => 'completed'])->assertOk();
    $this->patchJson(route('admin.contact-requests.update', $contact), ['contact_status' => 'approved'])->assertUnprocessable();

    $this->assertDatabaseHas('donor_contact_requests', ['id' => $contact->id, 'contact_status' => 'completed']);
});

it('returns individual donations and contact requests for AJAX previews', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $donation = BloodDonation::factory()->create();
    $contact = DonorContactRequest::factory()->create();

    $this->actingAs($admin)->getJson(route('admin.donations.show', $donation))
        ->assertOk()->assertJsonPath('data.id', $donation->id)
        ->assertJsonPath('data.bloodDonor.id', $donation->blood_donor_id);
    $this->getJson(route('admin.contact-requests.show', $contact))
        ->assertOk()->assertJsonPath('data.id', $contact->id)
        ->assertJsonPath('data.blood_donor.id', $contact->blood_donor_id);
});

it('creates requests with an initial status log and refuses direct fulfillment', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();

    $response = $this->actingAs($admin)->postJson(route('admin.blood-requests.store'), [
        'blood_group_id' => $group->id, 'patient_name' => 'Patient', 'contact_name' => 'Contact',
        'contact_phone' => '01712345678', 'hospital_name' => 'Hospital', 'bags_needed' => 2, 'needed_at' => now()->addDay()->toDateTimeString(),
    ])->assertCreated();

    $id = $response->json('data.id');
    $this->assertDatabaseHas('blood_request_status_logs', ['blood_request_id' => $id, 'to_status' => 'pending']);
    $this->patchJson(route('admin.blood-requests.update', $id), ['request_status' => 'fulfilled'])->assertUnprocessable();
    $this->assertDatabaseHas('blood_requests', ['id' => $id, 'request_status' => 'pending']);
});

it('scopes matching responses to their blood request', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $request = BloodRequest::factory()->verified()->create(['blood_group_id' => $group->id]);
    $other = BloodRequest::factory()->verified()->create(['blood_group_id' => $group->id]);
    $donor = BloodDonor::factory()->create(['blood_group_id' => $group->id]);
    $match = RequestDonor::factory()->create(['blood_request_id' => $other->id, 'blood_donor_id' => $donor->id]);

    $this->actingAs($admin)->patchJson(route('admin.blood-requests.respond', [$request, $match->id]), ['response_status' => 'accepted'])->assertNotFound();

    $this->assertDatabaseHas('request_donors', ['id' => $match->id, 'response_status' => 'pending']);
});

it('returns real dashboard totals and updates settings', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    BloodDonor::factory()->create();
    $setting = SystemSetting::factory()->create();

    $this->actingAs($admin)->getJson(route('admin.dashboard'))->assertOk()->assertJsonPath('data.donors', 1)->assertJsonCount(6, 'data.monthly_donations');
    $this->patchJson(route('admin.settings.update', $setting), ['value' => 'Updated site'])->assertOk();

    $this->assertDatabaseHas('system_settings', ['id' => $setting->id, 'value' => 'Updated site']);
});

it('allows only administrators to sign in', function (bool $isAdmin) {
    $user = User::factory()->create(['is_admin' => $isAdmin]);

    $response = $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password']);

    if ($isAdmin) {
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    } else {
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
})->with([true, false]);

it('keeps a request open after a partial donation', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $donor = BloodDonor::factory()->create(['blood_group_id' => $group->id]);
    $request = BloodRequest::factory()->verified()->create(['blood_group_id' => $group->id, 'bags_needed' => 2]);

    $response = $this->actingAs($admin)->postJson(route('admin.donations.store'), ['blood_donor_id' => $donor->id,
        'blood_request_id' => $request->id, 'donated_at' => today()->toDateString(), 'bags' => 1])->assertCreated();
    $this->postJson(route('admin.donations.verify', $response->json('data.id')))->assertOk();

    $this->assertDatabaseHas('blood_requests', ['id' => $request->id, 'request_status' => 'in_progress']);
    $this->assertDatabaseHas('blood_request_status_logs', ['blood_request_id' => $request->id, 'to_status' => 'in_progress']);
});

it('rejects donations against unverified or closed requests', function (string $condition) {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $donor = BloodDonor::factory()->create(['blood_group_id' => $group->id, 'is_available' => $condition !== 'unavailable']);
    $request = BloodRequest::factory()->create(['blood_group_id' => $group->id, 'request_status' => $condition === 'closed' ? 'cancelled' : 'pending']);

    $this->actingAs($admin)->postJson(route('admin.donations.store'), ['blood_donor_id' => $donor->id,
        'blood_request_id' => $request->id, 'donated_at' => today()->toDateString(), 'bags' => 1])->assertUnprocessable();

    $this->assertDatabaseCount('blood_donations', 0);
    $this->assertDatabaseCount('request_donors', 0);
    $this->assertDatabaseCount('blood_request_status_logs', 0);
})->with(['unverified', 'closed']);

it('rejects invalid donation dates and bag counts', function (array $invalid, string $field) {
    $this->travelTo(now()->setDate(2026, 10, 27)->startOfDay());
    $admin = User::factory()->create(['is_admin' => true]);
    $donor = BloodDonor::factory()->create();

    $this->actingAs($admin)->postJson(route('admin.donations.store'), array_merge([
        'blood_donor_id' => $donor->id, 'donated_at' => '2026-10-27', 'bags' => 1,
    ], $invalid))->assertUnprocessable()->assertJsonValidationErrors($field);

    $this->assertDatabaseCount('blood_donations', 0);
    expect($donor->fresh()->is_available)->toBeTrue();
})->with([
    'future date' => [['donated_at' => '2026-10-28'], 'donated_at'],
    'multiple bags for one donor' => [['bags' => 2], 'bags'],
    'zero bags' => [['bags' => 0], 'bags'],
]);

it('rejects duplicate donations without inserting another history record', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $donor = BloodDonor::factory()->create(['blood_group_id' => $group->id]);
    $request = BloodRequest::factory()->verified()->create(['blood_group_id' => $group->id, 'bags_needed' => 2]);
    $data = ['blood_donor_id' => $donor->id, 'blood_request_id' => $request->id, 'donated_at' => today()->toDateString(), 'bags' => 1];
    $response = $this->actingAs($admin)->postJson(route('admin.donations.store'), $data)->assertCreated();
    $this->postJson(route('admin.donations.verify', $response->json('data.id')))->assertOk();

    $this->postJson(route('admin.donations.store'), $data)->assertUnprocessable();

    $this->assertDatabaseCount('blood_donations', 1);
    $this->assertDatabaseCount('donor_availability_logs', 1);
});

it('preserves location hierarchy validation on partial donor updates', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $upazila = Upazila::factory()->create();
    $other = District::factory()->create();
    $donor = BloodDonor::factory()->create(['district_id' => $upazila->district_id, 'upazila_id' => $upazila->id]);

    $this->actingAs($admin)->patchJson(route('admin.blood-donors.update', $donor), ['district_id' => $other->id])
        ->assertUnprocessable()->assertJsonValidationErrors('upazila_id');

    $this->assertDatabaseHas('blood_donors', ['id' => $donor->id, 'district_id' => $upazila->district_id]);
});

it('creates locations and returns only the requested children', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $district = District::factory()->create();
    $other = Upazila::factory()->create();

    $response = $this->actingAs($admin)->postJson(route('admin.locations.store', 'upazilas'), ['district_id' => $district->id, 'name' => 'Manda', 'name_bn' => 'মান্দা'])->assertCreated();

    $this->getJson(route('locations.index', ['district_id' => $district->id]))->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $response->json('data.id'));
    $this->assertDatabaseHas('upazilas', ['name' => 'Manda', 'district_id' => $district->id]);
});

it('filters admin donors without hiding registrations pending approval', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $group = BloodGroup::factory()->create();
    $donor = BloodDonor::factory()->create(['blood_group_id' => $group->id, 'name' => 'Review Donor', 'status' => 0]);
    BloodDonor::factory()->create(['blood_group_id' => $group->id, 'name' => 'Other Donor']);

    $this->actingAs($admin)->getJson(route('admin.blood-donors.index', ['q' => 'Review']))->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $donor->id);
});

it('grants admin access to an existing user through the explicit command', function () {
    $user = User::factory()->create();

    $this->artisan('app:create-admin', ['email' => $user->email, '--no-interaction' => true])->assertSuccessful();

    expect($user->fresh()->is_admin)->toBeTrue();
});

it('does not generate an unattended admin password', function () {
    $this->artisan('app:create-admin', ['email' => 'admin@example.test', '--no-interaction' => true])->assertFailed();

    $this->assertDatabaseCount('users', 0);
});

it('restores both deletion markers without making the record inaccessible', function () {
    $donor = BloodDonor::factory()->create();
    $donor->delete();

    $archived = BloodDonor::withTrashed()->findOrFail($donor->id);
    $archived->restore();

    $this->assertDatabaseHas('blood_donors', ['id' => $donor->id, 'deleted' => 0, 'deleted_at' => null, 'deleted_by' => null]);
    expect(BloodDonor::find($donor->id))->not->toBeNull();
});

it('returns not found for the removed admin activity endpoint', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->getJson('/admin/activity-logs')->assertNotFound();
});
