<?php

use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('filters blood requests before pagination', function (string $tab, array $expectedNames) {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    foreach ([
        ['patient_name' => 'Pending patient', 'request_status' => 'pending', 'urgency' => 'normal'],
        ['patient_name' => 'Emergency patient', 'request_status' => 'verified', 'urgency' => 'emergency'],
        ['patient_name' => 'Complete patient', 'request_status' => 'fulfilled', 'urgency' => 'urgent'],
        ['patient_name' => 'Expired patient', 'request_status' => 'expired', 'urgency' => 'critical'],
    ] as $attributes) {
        BloodRequest::factory()->create($attributes);
    }

    $response = $this->getJson(route('admin.blood-requests.index', ['tab' => $tab]))
        ->assertOk()
        ->assertJsonPath('meta.total', count($expectedNames));

    expect(array_column($response->json('data'), 'patient_name'))->toEqualCanonicalizing($expectedNames);
})->with([
    'all' => ['all', ['Pending patient', 'Emergency patient', 'Complete patient', 'Expired patient']],
    'pending' => ['pending', ['Pending patient']],
    'emergency' => ['emergency', ['Emergency patient']],
    'normal' => ['normal', ['Pending patient']],
    'complete' => ['complete', ['Complete patient']],
    'expired' => ['expired', ['Expired patient']],
]);

it('shows all six request filters and highlights the selected tab', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));

    $this->get(route('admin.blood-requests.index', ['tab' => 'pending']))
        ->assertOk()
        ->assertSee('request-tabs')
        ->assertSeeInOrder(['tab=all', 'tab=pending', 'aria-current="page"', 'tab=emergency', 'tab=normal', 'tab=complete', 'tab=expired'], false);
});
