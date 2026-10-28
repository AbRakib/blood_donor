<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

dataset('backend pages', [
    'dashboard' => ['dashboard', 'dashboard', 'dashboardExtra'],
    'donors' => ['admin.blood-donors.index', 'admin.donors', 'directory'],
    'requests' => ['admin.blood-requests.index', 'admin.requests', 'requestsView'],
    'blood groups' => ['admin.blood-groups.index', 'admin.groups', 'groupsView'],
    'contacts' => ['admin.contact-requests.index', 'admin.contacts', 'contactsView'],
    'donations' => ['admin.donations.index', 'admin.donations', 'donationsView'],
    'reports' => ['admin.reports.index', 'admin.reports', 'dashboardExtra'],
]);

dataset('protected backend routes', [
    'dashboard', 'admin.blood-donors.index', 'admin.blood-requests.index',
    'admin.blood-groups.index', 'admin.contact-requests.index', 'admin.donations.index',
    'admin.reports.index', 'admin.settings.index',
]);

it('renders a dedicated backend view for each page', function (string $route, string $view, string $section) {
    $admin = User::factory()->make(['is_admin' => true]);

    $this->actingAs($admin)->get(route($route))
        ->assertOk()->assertViewIs($view)->assertSee('id="'.$section.'"', false)
        ->assertSee('href="'.route('admin.settings.index').'"', false)
        ->assertDontSee('id="settingsForm"', false)->assertDontSee('data-page=', false);
})->with('backend pages');

it('renders only the requested backend page content', function () {
    $admin = User::factory()->make(['is_admin' => true]);

    $this->actingAs($admin)->get(route('admin.blood-donors.index'))
        ->assertOk()->assertSee('id="directory"', false)
        ->assertDontSee('id="requestsView"', false)
        ->assertDontSee('id="dashboardExtra"', false)
        ->assertDontSee('id="contactsView"', false);
});

it('renders settings values and escapes user input', function () {
    $admin = User::factory()->make(['is_admin' => true]);
    $setting = Setting::factory()->make(['name' => '<script>alert(1)</script>', 'status' => 0]);
    $this->actingAs($admin);

    $this->view('admin.settings', ['setting' => $setting])
        ->assertSee('id="settingsForm"', false)->assertSee('<script>alert(1)</script>')
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee('type="hidden" name="status" value="0"', false)
        ->assertDontSee('id="setting-status"', false)
        ->assertDontSee('<h2>'.__('System').'</h2>', false)
        ->assertDontSee('id="directory"', false)->assertDontSee('id="requestsView"', false);
});

it('renders an empty settings form before settings have been saved', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->get(route('admin.settings.index'))
        ->assertOk()->assertViewIs('admin.settings')->assertViewHas('setting', null)
        ->assertSee('name="status"', false)->assertSee('id="settingsLiveName"', false);
});

it('redirects guests to login for backend pages', function (string $route) {
    $this->get(route($route))->assertRedirect(route('login'));
})->with('protected backend routes');

it('returns 403 for nonadministrators visiting backend pages', function (string $route) {
    $user = User::factory()->make(['is_admin' => false]);

    $this->actingAs($user)->get(route($route))->assertForbidden();
})->with('protected backend routes');

it('redirects old dashboard page links to their dedicated routes', function (string $page, string $route) {
    $admin = User::factory()->make(['is_admin' => true]);

    $this->actingAs($admin)->get(route('dashboard', ['page' => $page]))->assertRedirect(route($route));
})->with([
    ['dashboard', 'dashboard'],
    ['donors', 'admin.blood-donors.index'],
    ['requests', 'admin.blood-requests.index'],
    ['groups', 'admin.blood-groups.index'],
    ['contacts', 'admin.contact-requests.index'],
    ['donations', 'admin.donations.index'],
    ['reports', 'admin.reports.index'],
    ['settings', 'admin.settings.index'],
]);

it('renders the dashboard for an unknown legacy page', function () {
    $admin = User::factory()->make(['is_admin' => true]);

    $this->actingAs($admin)->get(route('dashboard', ['page' => 'unknown']))
        ->assertOk()->assertViewIs('dashboard');
});
