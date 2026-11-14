<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    public function index(): View
    {
        return view('admin.vehicles', [
            'vehicles' => Vehicle::with('driver:id,name,phone')->latest('id')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.vehicle-form', [
            'vehicle' => new Vehicle,
            'drivers' => Driver::active()->orderBy('name')->get(['id', 'name', 'phone']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Vehicle::create($this->validatedData($request));

        return to_route('admin.vehicles.index')->with('success', __('Vehicle created successfully.'));
    }

    public function show(Vehicle $vehicle): View
    {
        return view('admin.vehicle-show', ['vehicle' => $vehicle->load('driver:id,name,phone')]);
    }

    public function edit(Vehicle $vehicle): View
    {
        return view('admin.vehicle-form', [
            'vehicle' => $vehicle,
            'drivers' => Driver::query()->where(function (EloquentBuilder $query) use ($vehicle): void {
                $query->where('status', 1)->orWhere('id', $vehicle->driver_id);
            })->orderBy('name')->get(['id', 'name', 'phone']),
        ]);
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->updateOrFail($this->validatedData($request, $vehicle));

        return to_route('admin.vehicles.index')->with('success', __('Vehicle updated successfully.'));
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->deleteOrFail();

        return to_route('admin.vehicles.index')->with('success', __('Vehicle deleted successfully.'));
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request, ?Vehicle $vehicle = null): array
    {
        return $request->validate([
            'driver_id' => ['required', 'integer', Rule::exists('drivers', 'id')->where('deleted', 0)->whereNull('deleted_at')->where(function (QueryBuilder $query) use ($vehicle): void {
                $query->where('status', 1);
                if ($vehicle !== null) {
                    $query->orWhere('id', $vehicle->driver_id);
                }
            })],
            'vehicle_type' => ['required', 'string', 'max:255'],
            'vehicle_number' => ['required', 'string', 'max:255'],
            'vehicle_model' => ['nullable', 'string', 'max:255'],
            'vehicle_color' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'integer', 'in:0,1'],
        ]);
    }
}
