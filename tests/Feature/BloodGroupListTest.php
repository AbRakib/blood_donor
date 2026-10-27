<?php

use App\Models\BloodGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('includes the creation date and creator name for administrators', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);
    BloodGroup::factory()->create(['name' => 'A+', 'created_at' => '2026-10-27 06:00:00']);

    $this->getJson(route('blood-groups.index'))->assertOk()
        ->assertJsonPath('data.0.created_at', '2026-10-27T06:00:00.000000Z')
        ->assertJsonPath('data.0.creator.name', $admin->name)
        ->assertJsonMissingPath('data.0.creator.email');
});

it('returns a null creator for system seeded blood groups', function () {
    BloodGroup::factory()->create(['name' => 'A+']);
    $this->actingAs(User::factory()->create(['is_admin' => true]));

    $this->getJson(route('blood-groups.index'))->assertOk()
        ->assertJsonPath('data.0.creator', null)
        ->assertJsonPath('data.0.created_by', null);
});

it('keeps creation details out of the public blood group list', function () {
    BloodGroup::factory()->create(['name' => 'A+']);

    $this->getJson(route('blood-groups.index'))->assertOk()
        ->assertJsonPath('data.0.name', 'A+')
        ->assertJsonMissingPath('data.0.created_at')
        ->assertJsonMissingPath('data.0.created_by')
        ->assertJsonMissingPath('data.0.creator');
});

it('keeps creation details out of the list for non administrators', function () {
    BloodGroup::factory()->create(['name' => 'A+']);
    $this->actingAs(User::factory()->create(['is_admin' => false]));

    $this->getJson(route('blood-groups.index'))->assertOk()
        ->assertJsonMissingPath('data.0.created_at')
        ->assertJsonMissingPath('data.0.creator');
});
