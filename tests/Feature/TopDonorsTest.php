<?php

use App\Models\BloodDonor;
use App\Models\BloodGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the ten leading active donors by verified donation totals after the hero', function () {
    $group = BloodGroup::factory()->create();
    $donors = BloodDonor::factory()->count(11)->for($group)->sequence(
        ...array_map(fn (int $count): array => ['name' => 'Ranked donor '.$count, 'total_donations' => $count], range(1, 11))
    )->create();
    BloodDonor::factory()->for($group)->create(['name' => 'Inactive donor', 'status' => 0, 'total_donations' => 100]);
    $deleted = BloodDonor::factory()->for($group)->create(['name' => 'Deleted donor', 'total_donations' => 100]);
    $deleted->delete();
    BloodDonor::factory()->for($group)->create(['name' => 'New donor', 'total_donations' => 0]);
    $inactiveGroup = BloodGroup::factory()->create(['status' => 0]);
    BloodDonor::factory()->for($inactiveGroup)->create(['name' => 'Inactive blood group donor', 'total_donations' => 100]);

    $response = $this->get(route('home'))->assertOk()
        ->assertSeeInOrder(['id="home"', 'id="top-donors"', 'id="donors"'], false)
        ->assertSeeInOrder(array_map(fn (int $count): string => 'Ranked donor '.$count, range(11, 2)))
        ->assertDontSee('Inactive donor')->assertDontSee('Deleted donor')
        ->assertDontSee('Inactive blood group donor')->assertDontSee('New donor');

    expect($response->viewData('topDonors')->modelKeys())->toBe($donors->reverse()->take(10)->modelKeys());
    $response->assertDontSee($donors->last()->phone);
});

it('shows an empty state when no active donors exist', function () {
    $this->get(route('home'))->assertOk()
        ->assertSee('Our top donors will appear here after their first verified donation.');
});

it('translates the top donors section into Bengali', function () {
    $this->withSession(['locale' => 'bn'])->get(route('home'))->assertOk()
        ->assertSee('সেরা ১০ জন রক্তদাতা');
});

it('shows up to ten random active donors when no donors have verified donations', function () {
    $group = BloodGroup::factory()->create();
    $donors = BloodDonor::factory()->count(12)->for($group)->create(['total_donations' => 0]);
    BloodDonor::factory()->for($group)->create(['name' => 'Inactive fallback donor', 'status' => 0]);

    $response = $this->get(route('home'))->assertOk()->assertDontSee('Inactive fallback donor');
    $shown = $response->viewData('topDonors');

    expect($shown)->toHaveCount(10);
    expect($shown->modelKeys())->each->toBeIn($donors->modelKeys());
    foreach ($shown as $donor) {
        $response->assertSee($donor->name);
    }
});

it('replaces the random fallback with donors ranked by donation count', function () {
    $group = BloodGroup::factory()->create();
    BloodDonor::factory()->count(2)->for($group)->create(['total_donations' => 0]);
    $firstDonor = BloodDonor::factory()->for($group)->create(['total_donations' => 1]);
    $leadingDonor = BloodDonor::factory()->for($group)->create(['total_donations' => 5]);

    $response = $this->get(route('home'))->assertOk();

    expect($response->viewData('topDonors')->modelKeys())->toBe([$leadingDonor->id, $firstDonor->id]);
});
