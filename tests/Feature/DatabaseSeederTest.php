<?php

use App\Models\ActivityLog;
use App\Models\BloodDonation;
use App\Models\BloodDonor;
use App\Models\BloodGroup;
use App\Models\BloodRequest;
use App\Models\BloodRequestStatusLog;
use App\Models\DonorAvailabilityLog;
use App\Models\DonorContactRequest;
use App\Models\RequestDonor;
use Database\Seeders\BloodGroupSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('seeds every domain model without duplicating records on subsequent runs', function () {
    $this->freezeTime();
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    foreach ([BloodGroup::class, BloodDonor::class, BloodRequest::class, RequestDonor::class, BloodDonation::class, BloodRequestStatusLog::class, DonorAvailabilityLog::class, DonorContactRequest::class, ActivityLog::class] as $model) {
        $this->assertDatabaseCount((new $model)->getTable(), 8);
    }

    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseCount('districts', 1);
    $this->assertDatabaseCount('upazilas', 1);
    $this->assertDatabaseCount('unions', 2);
    $this->assertDatabaseCount('system_settings', 2);

    $donation = BloodDonation::firstOrFail();
    expect($donation->bloodDonor->blood_group_id)->toBe($donation->bloodRequest->blood_group_id);
});

it('updates existing reference records through their models', function () {
    $group = BloodGroup::factory()->create(['name' => 'A+', 'sort_order' => 99]);

    $this->seed(BloodGroupSeeder::class);

    expect($group->refresh()->sort_order)->toBe(0);
    $this->assertDatabaseCount('blood_groups', 8);
});

it('creates factories with valid relationships after reference data has been seeded', function () {
    $this->seed(BloodGroupSeeder::class);

    BloodDonor::factory()->count(12)->create();
    BloodRequest::factory()->count(12)->create();
    $log = ActivityLog::factory()->create();

    $this->assertDatabaseCount('blood_groups', 8);
    $this->assertDatabaseHas('blood_donors', ['id' => $log->subject_id]);
});
