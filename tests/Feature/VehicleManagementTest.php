<?php

use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('lets administrators manage vehicles through the dashboard', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $driver = Driver::factory()->create();
    $this->actingAs($admin);

    $this->get(route('admin.vehicles.index'))->assertOk()->assertSee('No vehicles found.');
    $this->get(route('admin.vehicles.create'))->assertOk()->assertSee($driver->name);
    $data = ['driver_id' => $driver->id, 'vehicle_type' => 'ambulance', 'vehicle_number' => 'DHAKA-1234', 'vehicle_model' => 'Hiace', 'vehicle_color' => 'White', 'status' => 1];
    $this->post(route('admin.vehicles.store'), $data)->assertRedirect(route('admin.vehicles.index'));
    $vehicle = Vehicle::sole();
    expect($vehicle->created_by)->toBe($admin->id);
    $this->get(route('admin.vehicles.index'))->assertOk()->assertSee('DHAKA-1234')->assertSee($driver->name);
    $this->get(route('admin.vehicles.show', $vehicle))->assertOk()->assertSee('Hiace');
    $this->get(route('admin.vehicles.edit', $vehicle))->assertOk()->assertSee('DHAKA-1234');
    $this->put(route('admin.vehicles.update', $vehicle), [...$data, 'vehicle_color' => 'Blue', 'status' => 0])->assertRedirect(route('admin.vehicles.index'));
    expect($vehicle->fresh())->vehicle_color->toBe('Blue')->status->toBe(0);
    $this->delete(route('admin.vehicles.destroy', $vehicle))->assertRedirect(route('admin.vehicles.index'));
    $this->assertSoftDeleted($vehicle);
    $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id, 'deleted' => 1, 'deleted_by' => $admin->id]);
    $this->get(route('admin.vehicles.show', $vehicle))->assertNotFound();
    $this->get(route('admin.vehicles.edit', $vehicle))->assertNotFound();
    $this->put(route('admin.vehicles.update', $vehicle), $data)->assertNotFound();
    $this->get(route('admin.vehicles.index'))->assertDontSee('DHAKA-1234');
});

it('protects every vehicle operation from guests and nonadministrators', function () {
    $vehicle = Vehicle::factory()->create();
    $requests = [
        ['GET', route('admin.vehicles.index')], ['GET', route('admin.vehicles.create')],
        ['POST', route('admin.vehicles.store')], ['GET', route('admin.vehicles.show', $vehicle)],
        ['GET', route('admin.vehicles.edit', $vehicle)], ['PUT', route('admin.vehicles.update', $vehicle)],
        ['DELETE', route('admin.vehicles.destroy', $vehicle)],
    ];
    foreach ($requests as [$method, $url]) {
        $this->call($method, $url)->assertRedirect(route('login'));
    }
    $this->actingAs(User::factory()->create(['is_admin' => false]));
    foreach ($requests as [$method, $url]) {
        $this->call($method, $url)->assertForbidden();
    }
});

it('validates vehicle fields without changing stored records', function () {
    $vehicle = Vehicle::factory()->create();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->postJson(route('admin.vehicles.store'), [])->assertUnprocessable()->assertJsonValidationErrors(['driver_id', 'vehicle_type', 'vehicle_number', 'status']);
    $this->putJson(route('admin.vehicles.update', $vehicle), [
        'driver_id' => 999999, 'vehicle_type' => str_repeat('a', 256), 'vehicle_number' => '',
        'vehicle_model' => [], 'vehicle_color' => str_repeat('a', 256), 'status' => 2,
    ])->assertUnprocessable()->assertJsonValidationErrors(['driver_id', 'vehicle_type', 'vehicle_number', 'vehicle_model', 'vehicle_color', 'status']);
    expect(Vehicle::count())->toBe(1);
    expect($vehicle->fresh()->status)->toBe(1);
});

it('rejects inactive and deleted drivers when assigning a vehicle', function (string $state) {
    $driver = Driver::factory()->create(['status' => $state === 'inactive' ? 0 : 1]);
    if ($state === 'deleted') {
        $driver->delete();
    }
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->postJson(route('admin.vehicles.store'), [
        'driver_id' => $driver->id, 'vehicle_type' => 'ambulance', 'vehicle_number' => 'TEST-123', 'status' => 1,
    ])->assertUnprocessable()->assertJsonValidationErrors('driver_id');
    $this->get(route('admin.vehicles.create'))->assertDontSee($driver->name);
    expect(Vehicle::count())->toBe(0);
})->with(['inactive', 'deleted']);

it('keeps the current inactive driver available when editing', function () {
    $vehicle = Vehicle::factory()->create();
    $vehicle->driver->update(['status' => 0]);
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->get(route('admin.vehicles.edit', $vehicle))->assertOk()->assertSee($vehicle->driver->name);
    $this->put(route('admin.vehicles.update', $vehicle), [
        'driver_id' => $vehicle->driver_id, 'vehicle_type' => 'ambulance', 'vehicle_number' => 'UPDATED-123', 'status' => 1,
    ])->assertRedirect(route('admin.vehicles.index'));
    expect($vehicle->fresh()->vehicle_number)->toBe('UPDATED-123');
});

it('escapes vehicle details and paginates the list', function () {
    $vehicle = Vehicle::factory()->create(['vehicle_model' => '<script>alert(1)</script>']);
    Vehicle::factory()->count(20)->create(['driver_id' => $vehicle->driver_id]);
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->get(route('admin.vehicles.show', $vehicle))->assertOk()->assertSee('<script>alert(1)</script>')->assertDontSee('<script>alert(1)</script>', false);
    $this->get(route('admin.vehicles.index'))->assertViewHas('vehicles', fn ($vehicles) => $vehicles->count() === 20 && $vehicles->total() === 21);
    $this->get(route('admin.vehicles.index', ['page' => 2]))->assertOk()->assertSee($vehicle->vehicle_number);
});
