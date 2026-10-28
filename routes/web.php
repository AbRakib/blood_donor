<?php

use App\Http\Controllers\Admin\BloodDonorController;
use App\Http\Controllers\Admin\BloodGroupController;
use App\Http\Controllers\Admin\BloodRequestController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DonationController;
use App\Http\Controllers\Admin\DonorContactRequestController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Public\DonorRegistrationController;
use App\Http\Controllers\Public\DonorSearchController;
use Illuminate\Support\Facades\Route;

Route::post('/language/{locale}', [LocaleController::class, 'update'])->whereIn('locale', ['en', 'bn'])->name('locale.update');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'can:admin'])->name('dashboard');

Route::get('/login', [AuthController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'store'])->middleware(['guest', 'throttle:5,1'])->name('login.store');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('blood-groups', [BloodGroupController::class, 'index'])->name('blood-groups.index');
Route::get('locations/districts', [LocationController::class, 'districts'])->name('locations.districts');
Route::get('locations/districts/{district}/upazilas', [LocationController::class, 'upazilas'])->name('locations.upazilas');
Route::get('locations/upazilas/{upazila}/unions', [LocationController::class, 'unions'])->name('locations.unions');
Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
Route::get('donors', [DonorSearchController::class, 'index'])->middleware('throttle:60,1')->name('donors.index');
Route::post('donors', [DonorRegistrationController::class, 'store'])->middleware('throttle:5,1')->name('donors.store');
Route::post('contact-requests', [DonorSearchController::class, 'contact'])->middleware('throttle:5,1')->name('contact-requests.store');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:admin'])->group(function (): void {
    Route::get('dashboard/statistics', [DashboardController::class, 'getStatistics'])->name('dashboard.statistics');
    Route::get('dashboard/recent-requests', [DashboardController::class, 'getRecentRequests'])->name('dashboard.recent-requests');
    Route::get('dashboard', [DashboardController::class, 'getOverview'])->name('dashboard');
    Route::patch('blood-donors/{blood_donor}/availability', [BloodDonorController::class, 'changeAvailability'])->name('blood-donors.availability');
    Route::get('blood-donors/{blood_donor}/history', [DonationController::class, 'history'])->name('blood-donors.history');
    Route::apiResource('blood-donors', BloodDonorController::class);
    Route::apiResource('blood-requests', BloodRequestController::class)->except('destroy');
    Route::post('blood-requests/{blood_request}/verify', [BloodRequestController::class, 'verify'])->name('blood-requests.verify');
    Route::post('blood-requests/{blood_request}/assign-donor', [BloodRequestController::class, 'assignDonor'])->name('blood-requests.assign-donor');
    Route::patch('blood-requests/{blood_request}/status', [BloodRequestController::class, 'changeStatus'])->name('blood-requests.status');
    Route::post('blood-requests/{blood_request}/matches', [BloodRequestController::class, 'match'])->name('blood-requests.matches');
    Route::patch('blood-requests/{blood_request}/matches/{requestDonor}', [BloodRequestController::class, 'respond'])->name('blood-requests.respond');
    Route::post('donations/{donation}/verify', [DonationController::class, 'verify'])->name('donations.verify');
    Route::apiResource('donations', DonationController::class)->only(['index', 'store']);
    Route::get('blood-groups', [BloodGroupController::class, 'page'])->name('blood-groups.index');
    Route::patch('blood-groups/{blood_group}', [BloodGroupController::class, 'update'])->name('blood-groups.update');
    Route::post('locations/{type}', [LocationController::class, 'store'])->whereIn('type', ['districts', 'upazilas', 'unions'])->name('locations.store');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::patch('settings', [SettingController::class, 'store'])->name('settings.store');
    Route::post('settings', [SettingController::class, 'store'])->name('settings.upload');
    Route::patch('settings/{system_setting}', [SettingController::class, 'update'])->name('settings.update');
    Route::get('contact-requests', [DonorContactRequestController::class, 'index'])->name('contact-requests.index');
    Route::patch('contact-requests/{contact_request}', [DonorContactRequestController::class, 'update'])->name('contact-requests.update');
});
