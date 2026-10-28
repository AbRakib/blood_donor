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

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'language'], function (): void {
    Route::post('/{locale}', [LocaleController::class, 'update'])
        ->whereIn('locale', ['en', 'bn'])
        ->name('locale.update');
});

Route::group(['middleware' => 'guest'], function (): void {
    Route::group(['prefix' => 'login'], function (): void {
        Route::get('/', [AuthController::class, 'create'])->name('login');
        Route::post('/', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    });
});

Route::group(['middleware' => 'auth'], function (): void {
    Route::post('logout', [AuthController::class, 'destroy'])->name('logout');
});

Route::group(['prefix' => 'blood-groups'], function (): void {
    Route::get('/', [BloodGroupController::class, 'index'])->name('blood-groups.index');
});

Route::group(['prefix' => 'locations'], function (): void {
    Route::get('/', [LocationController::class, 'index'])->name('locations.index');
    Route::get('/districts', [LocationController::class, 'districts'])->name('locations.districts');
    Route::get('/districts/{district}/upazilas', [LocationController::class, 'upazilas'])->name('locations.upazilas');
    Route::get('/upazilas/{upazila}/unions', [LocationController::class, 'unions'])->name('locations.unions');
});

Route::group(['prefix' => 'donors'], function (): void {
    Route::get('/', [DonorSearchController::class, 'index'])->middleware('throttle:60,1')->name('donors.index');
    Route::post('/', [DonorRegistrationController::class, 'store'])->middleware('throttle:5,1')->name('donors.store');
});

Route::group(['prefix' => 'contact-requests'], function (): void {
    Route::post('/', [DonorSearchController::class, 'contact'])->middleware('throttle:5,1')->name('contact-requests.store');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'can:admin'])->name('dashboard');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'can:admin']], function (): void {
    Route::group(['prefix' => 'dashboard'], function (): void {
        Route::get('/', [DashboardController::class, 'getOverview'])->name('dashboard');
        Route::get('/statistics', [DashboardController::class, 'getStatistics'])->name('dashboard.statistics');
        Route::get('/recent-requests', [DashboardController::class, 'getRecentRequests'])->name('dashboard.recent-requests');
    });

    Route::group(['prefix' => 'blood-donors'], function (): void {
        Route::get('/', [BloodDonorController::class, 'index'])->name('blood-donors.index');
        Route::post('/', [BloodDonorController::class, 'store'])->name('blood-donors.store');
        Route::get('/{blood_donor}', [BloodDonorController::class, 'show'])->name('blood-donors.show');
        Route::match(['put', 'patch'], '/{blood_donor}', [BloodDonorController::class, 'update'])->name('blood-donors.update');
        Route::delete('/{blood_donor}', [BloodDonorController::class, 'destroy'])->name('blood-donors.destroy');
        Route::patch('/{blood_donor}/availability', [BloodDonorController::class, 'changeAvailability'])->name('blood-donors.availability');
        Route::get('/{blood_donor}/history', [DonationController::class, 'history'])->name('blood-donors.history');
    });

    Route::group(['prefix' => 'blood-requests'], function (): void {
        Route::get('/', [BloodRequestController::class, 'index'])->name('blood-requests.index');
        Route::post('/', [BloodRequestController::class, 'store'])->name('blood-requests.store');
        Route::get('/{blood_request}', [BloodRequestController::class, 'show'])->name('blood-requests.show');
        Route::match(['put', 'patch'], '/{blood_request}', [BloodRequestController::class, 'update'])->name('blood-requests.update');
        Route::post('/{blood_request}/verify', [BloodRequestController::class, 'verify'])->name('blood-requests.verify');
        Route::post('/{blood_request}/assign-donor', [BloodRequestController::class, 'assignDonor'])->name('blood-requests.assign-donor');
        Route::patch('/{blood_request}/status', [BloodRequestController::class, 'changeStatus'])->name('blood-requests.status');
        Route::post('/{blood_request}/matches', [BloodRequestController::class, 'match'])->name('blood-requests.matches');
        Route::patch('/{blood_request}/matches/{requestDonor}', [BloodRequestController::class, 'respond'])->name('blood-requests.respond');
    });

    Route::group(['prefix' => 'donations'], function (): void {
        Route::get('/', [DonationController::class, 'index'])->name('donations.index');
        Route::post('/', [DonationController::class, 'store'])->name('donations.store');
        Route::get('/{donation}', [DonationController::class, 'show'])->name('donations.show');
        Route::post('/{donation}/verify', [DonationController::class, 'verify'])->name('donations.verify');
    });

    Route::group(['prefix' => 'blood-groups'], function (): void {
        Route::get('/', [BloodGroupController::class, 'page'])->name('blood-groups.index');
        Route::patch('/{blood_group}', [BloodGroupController::class, 'update'])->name('blood-groups.update');
    });

    Route::group(['prefix' => 'locations'], function (): void {
        Route::post('/{type}', [LocationController::class, 'store'])
            ->whereIn('type', ['districts', 'upazilas', 'unions'])
            ->name('locations.store');
    });

    Route::group(['prefix' => 'reports'], function (): void {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
    });

    Route::group(['prefix' => 'settings'], function (): void {
        Route::get('/', [SettingController::class, 'index'])->name('settings.index');
        Route::patch('/', [SettingController::class, 'store'])->name('settings.store');
        Route::post('/', [SettingController::class, 'store'])->name('settings.upload');
        Route::patch('/{system_setting}', [SettingController::class, 'update'])->name('settings.update');
    });

    Route::group(['prefix' => 'contact-requests'], function (): void {
        Route::get('/', [DonorContactRequestController::class, 'index'])->name('contact-requests.index');
        Route::get('/{contact_request}', [DonorContactRequestController::class, 'show'])->name('contact-requests.show');
        Route::patch('/{contact_request}', [DonorContactRequestController::class, 'update'])->name('contact-requests.update');
    });
});
