<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
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

Route::get('blood-groups', [Admin\BloodGroupController::class, 'index'])->name('blood-groups.index');
Route::get('locations/districts', [Admin\LocationController::class, 'districts'])->name('locations.districts');
Route::get('locations/districts/{district}/upazilas', [Admin\LocationController::class, 'upazilas'])->name('locations.upazilas');
Route::get('locations/upazilas/{upazila}/unions', [Admin\LocationController::class, 'unions'])->name('locations.unions');
Route::get('locations', [Admin\LocationController::class, 'index'])->name('locations.index');
Route::get('donors', [DonorSearchController::class, 'index'])->middleware('throttle:60,1')->name('donors.index');
Route::post('donors', [DonorRegistrationController::class, 'store'])->middleware('throttle:5,1')->name('donors.store');
Route::post('contact-requests', [DonorSearchController::class, 'contact'])->middleware('throttle:5,1')->name('contact-requests.store');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:admin'])->group(function (): void {
    Route::get('dashboard/statistics', [Admin\DashboardController::class, 'getStatistics'])->name('dashboard.statistics');
    Route::get('dashboard/recent-requests', [Admin\DashboardController::class, 'getRecentRequests'])->name('dashboard.recent-requests');
    Route::get('dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::patch('blood-donors/{blood_donor}/availability', [Admin\BloodDonorController::class, 'changeAvailability'])->name('blood-donors.availability');
    Route::get('blood-donors/{blood_donor}/history', [Admin\DonationController::class, 'history'])->name('blood-donors.history');
    Route::apiResource('blood-donors', Admin\BloodDonorController::class);
    Route::apiResource('blood-requests', Admin\BloodRequestController::class)->except('destroy');
    Route::post('blood-requests/{blood_request}/verify', [Admin\BloodRequestController::class, 'verify'])->name('blood-requests.verify');
    Route::post('blood-requests/{blood_request}/assign-donor', [Admin\BloodRequestController::class, 'assignDonor'])->name('blood-requests.assign-donor');
    Route::patch('blood-requests/{blood_request}/status', [Admin\BloodRequestController::class, 'changeStatus'])->name('blood-requests.status');
    Route::post('blood-requests/{blood_request}/matches', [Admin\BloodRequestController::class, 'match'])->name('blood-requests.matches');
    Route::patch('blood-requests/{blood_request}/matches/{requestDonor}', [Admin\BloodRequestController::class, 'respond'])->name('blood-requests.respond');
    Route::post('donations/{donation}/verify', [Admin\DonationController::class, 'verify'])->name('donations.verify');
    Route::apiResource('donations', Admin\DonationController::class)->only(['index', 'store']);
    Route::get('blood-groups', [Admin\BloodGroupController::class, 'page'])->name('blood-groups.index');
    Route::patch('blood-groups/{blood_group}', [Admin\BloodGroupController::class, 'update'])->name('blood-groups.update');
    Route::post('locations/{type}', [Admin\LocationController::class, 'store'])->whereIn('type', ['districts', 'upazilas', 'unions'])->name('locations.store');
    Route::get('reports', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('settings', [Admin\SettingController::class, 'index'])->name('settings.index');
    Route::patch('settings', [Admin\SettingController::class, 'store'])->name('settings.store');
    Route::post('settings', [Admin\SettingController::class, 'store'])->name('settings.upload');
    Route::patch('settings/{system_setting}', [Admin\SettingController::class, 'update'])->name('settings.update');
    Route::get('contact-requests', [Admin\DonorContactRequestController::class, 'index'])->name('contact-requests.index');
    Route::patch('contact-requests/{contact_request}', [Admin\DonorContactRequestController::class, 'update'])->name('contact-requests.update');
});
