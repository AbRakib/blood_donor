<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

it('shows the saved settings name in the sidebar', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Setting::factory()->create(['name' => 'Community Blood Bank']);

    $this->actingAs($admin)->get(route('admin.blood-donors.index'))
        ->assertOk()->assertSee('<strong>Community Blood Bank</strong>', false);
});

it('shows the default sidebar name when settings are missing', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->get(route('admin.blood-donors.index'))
        ->assertOk()->assertSee('<strong>'.__('Blood Donation').'</strong>', false);
});

it('shows the default sidebar name when the saved name is blank', function (?string $name) {
    $admin = User::factory()->create(['is_admin' => true]);
    Setting::factory()->create(['name' => $name]);

    $this->actingAs($admin)->get(route('admin.blood-donors.index'))
        ->assertOk()->assertSee('<strong>'.__('Blood Donation').'</strong>', false);
})->with([null, '', '   ']);

it('escapes the saved sidebar name', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Setting::factory()->create(['name' => '<script>alert(1)</script>']);

    $this->actingAs($admin)->get(route('admin.blood-donors.index'))
        ->assertOk()->assertSee('<script>alert(1)</script>')
        ->assertDontSee('<script>alert(1)</script>', false);
});

it('creates and updates the website settings in the settings table', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin)->getJson(route('admin.settings.index'))->assertOk()->assertJsonPath('data', null);

    $this->patchJson(route('admin.settings.store'), ['name' => 'Blood donation', 'email' => 'contact@example.com', 'status' => 1, 'created_by' => 999])
        ->assertOk()->assertJsonPath('data.name', 'Blood donation');
    $this->assertDatabaseHas('settings', ['name' => 'Blood donation', 'created_by' => $admin->id, 'updated_by' => $admin->id]);

    $this->patchJson(route('admin.settings.store'), ['name' => 'Updated name', 'status' => 0])->assertOk();
    $this->assertDatabaseCount('settings', 1);
    $this->assertDatabaseHas('settings', ['name' => 'Updated name', 'status' => 0, 'email' => 'contact@example.com']);
    $this->getJson(route('admin.settings.index'))->assertOk()->assertJsonPath('data.name', 'Updated name');
});

it('rejects invalid settings without saving', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->patchJson(route('admin.settings.store'), ['email' => 'invalid', 'facebook' => 'javascript:alert(1)', 'phone' => str_repeat('1', 21), 'status' => 2])
        ->assertUnprocessable()->assertJsonValidationErrors(['email', 'facebook', 'phone', 'status']);

    $this->assertDatabaseCount('settings', 0);
});

it('protects website settings from guests and non administrators', function () {
    $this->getJson(route('admin.settings.index'))->assertUnauthorized();
    $this->patchJson(route('admin.settings.store'), ['status' => 1])->assertUnauthorized();
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)->getJson(route('admin.settings.index'))->assertForbidden();
    $this->patchJson(route('admin.settings.store'), ['status' => 1])->assertForbidden();
    $this->assertDatabaseCount('settings', 0);
});

it('uploads settings images and preserves them when only text changes', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['is_admin' => true]);
    $image = fn () => UploadedFile::fake()->createWithContent('logo.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII='));

    $response = $this->actingAs($admin)->post(route('admin.settings.upload'), [
        'status' => 1, 'logo' => $image(), 'footer_logo' => $image(), 'meta_icon' => $image(),
    ], ['Accept' => 'application/json'])->assertOk();

    foreach (['logo', 'footer_logo', 'meta_icon'] as $field) {
        $path = $response->json('data.'.$field);
        Storage::disk('public')->assertExists($path);
        $this->assertDatabaseHas('settings', [$field => $path]);
    }
    $this->patchJson(route('admin.settings.store'), ['status' => 1, 'name' => 'Updated'])->assertOk()
        ->assertJsonPath('data.logo', $response->json('data.logo'));
});

it('rejects unsafe and oversized settings uploads', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.settings.upload'), [
        'status' => 1,
        'logo' => UploadedFile::fake()->create('logo.php', 1, 'text/plain'),
        'footer_logo' => UploadedFile::fake()->create('logo.png', 2049, 'image/png'),
        'meta_icon' => UploadedFile::fake()->createWithContent('icon.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'),
    ], ['Accept' => 'application/json'])->assertUnprocessable()->assertJsonValidationErrors(['logo', 'footer_logo', 'meta_icon']);

    $this->assertDatabaseCount('settings', 0);
    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});
