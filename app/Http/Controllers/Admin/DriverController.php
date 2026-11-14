<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    public function index(): View
    {
        return view('admin.drivers', [
            'drivers' => Driver::query()->latest('id')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.driver-form', [
            'driver' => new Driver,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Driver::create($this->validatedData($request));

        return to_route('admin.drivers.index')->with('success', __('Driver created successfully.'));
    }

    public function show(Driver $driver): View
    {
        return view('admin.driver-show', ['driver' => $driver]);
    }

    public function edit(Driver $driver): View
    {
        return view('admin.driver-form', [
            'driver' => $driver,

        ]);
    }

    public function update(Request $request, Driver $driver): RedirectResponse
    {
        $driver->updateOrFail($this->validatedData($request, $driver));

        return to_route('admin.drivers.index')->with('success', __('Driver updated successfully.'));
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        $driver->deleteOrFail();

        return to_route('admin.drivers.index')->with('success', __('Driver deleted successfully.'));
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request, ?Driver $driver = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', Rule::unique('drivers', 'phone')->ignore($driver)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('drivers', 'email')->ignore($driver)],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'present_address' => ['nullable', 'string', 'max:5000'],
            'permanent_address' => ['nullable', 'string', 'max:5000'],
            'city' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'nid_number' => ['nullable', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:255'],
            'license_type' => ['nullable', 'string', 'max:255'],
            'license_issue_date' => ['nullable', 'date'],
            'license_expiry_date' => ['nullable', 'date', 'after_or_equal:license_issue_date'],
            'status' => ['required', 'integer', 'in:0,1'],
        ]);
    }
}
