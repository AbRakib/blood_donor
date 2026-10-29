<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin', fn (User $user): bool => (bool) $user->is_admin);

        View::composer('admin.layouts.admin', function (ViewInstance $view): void {
            $name = trim((string) Setting::query()->orderBy('id')->value('name'));

            $view->with('sidebarName', $name !== '' ? $name : __('Blood Donation'));
        });
    }
}
