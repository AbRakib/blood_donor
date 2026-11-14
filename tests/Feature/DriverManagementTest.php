<?php

use App\Models\Driver;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('lets administrators manage drivers through the dashboard', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    $this->get(route('admin.drivers.index'))->assertOk()->assertSee('No drivers found.');
    $this->get(route('admin.drivers.create'))->assertOk()->assertSee('Add driver');
    $data = ['name' => 'Test Driver', 'phone' => '01712345678', 'email' => 'driver@example.com', 'license_number' => 'LICENSE-123', 'status' => 1];
    $this->post(route('admin.drivers.store'), $data)->assertRedirect(route('admin.drivers.index'));
    $driver = Driver::sole();
    expect($driver->created_by)->toBe($admin->id);
    $this->get(route('admin.drivers.index'))->assertOk()->assertSee('Test Driver')->assertSee($driver->name);
    $this->get(route('admin.drivers.show', $driver))->assertOk()->assertSee('LICENSE-123');
    $this->get(route('admin.drivers.edit', $driver))->assertOk()->assertSee('Test Driver');
    $this->put(route('admin.drivers.update', $driver), [...$data, 'name' => 'Updated Driver', 'status' => 0])->assertRedirect(route('admin.drivers.index'));
    expect($driver->fresh())->name->toBe('Updated Driver')->status->toBe(0);
    $this->delete(route('admin.drivers.destroy', $driver))->assertRedirect(route('admin.drivers.index'));
    $this->assertSoftDeleted($driver);
    $this->assertDatabaseHas('drivers', ['id' => $driver->id, 'deleted' => 1, 'deleted_by' => $admin->id]);
    $this->get(route('admin.drivers.show', $driver))->assertNotFound();
    $this->get(route('admin.drivers.edit', $driver))->assertNotFound();
    $this->put(route('admin.drivers.update', $driver), $data)->assertNotFound();
    $this->get(route('admin.drivers.index'))->assertDontSee('Test Driver');
});

it('protects every driver operation from guests and nonadministrators', function () {
    $driver = Driver::factory()->create();
    $requests = [
        ['GET', route('admin.drivers.index')], ['GET', route('admin.drivers.create')],
        ['POST', route('admin.drivers.store')], ['GET', route('admin.drivers.show', $driver)],
        ['GET', route('admin.drivers.edit', $driver)], ['PUT', route('admin.drivers.update', $driver)],
        ['DELETE', route('admin.drivers.destroy', $driver)],
    ];
    foreach ($requests as [$method, $url]) {
        $this->call($method, $url)->assertRedirect(route('login'));
    }
    $this->actingAs(User::factory()->create(['is_admin' => false]));
    foreach ($requests as [$method, $url]) {
        $this->call($method, $url)->assertForbidden();
    }
});

it('validates driver details and preserves records on failure', function () {
    $driver = Driver::factory()->create();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->postJson(route('admin.drivers.store'), [])->assertUnprocessable()->assertJsonValidationErrors(['name', 'phone', 'status']);
    $this->postJson(route('admin.drivers.store'), ['name' => 'Duplicate', 'phone' => $driver->phone, 'email' => $driver->email, 'status' => 1])
        ->assertUnprocessable()->assertJsonValidationErrors(['phone', 'email']);
    $this->putJson(route('admin.drivers.update', $driver), [
        'name' => '', 'phone' => str_repeat('1', 21), 'email' => 'invalid', 'status' => 2,
        'date_of_birth' => '2999-01-01', 'gender' => 'invalid',
        'license_issue_date' => '2026-01-01', 'license_expiry_date' => '2025-01-01',
    ])->assertUnprocessable()->assertJsonValidationErrors(['name', 'phone', 'email', 'status', 'date_of_birth', 'gender', 'license_expiry_date']);
    expect(Driver::count())->toBe(1);
    expect($driver->fresh()->name)->toBe($driver->name);
});

it('escapes driver details and paginates the list', function () {
    $driver = Driver::factory()->create(['name' => '<script>alert(1)</script>']);
    Driver::factory()->count(20)->create();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->get(route('admin.drivers.show', $driver))->assertOk()->assertSee($driver->name)->assertDontSee($driver->name, false);
    $this->get(route('admin.drivers.index'))->assertViewHas('drivers', fn ($drivers) => $drivers->count() === 20 && $drivers->total() === 21);
    $this->get(route('admin.drivers.index', ['page' => 2]))->assertOk()->assertSee($driver->name);
});
