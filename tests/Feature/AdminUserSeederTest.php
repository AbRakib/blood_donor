<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(LazilyRefreshDatabase::class);

it('creates the administrator with the configured password', function () {
    config(['auth.admin_password' => 'secure-admin-password']);

    $this->seed(AdminUserSeeder::class);

    $admin = User::where('email', 'admin@example.com')->sole();

    expect($admin)->name->toBe('Administrator')->is_admin->toBeTrue();
    expect(Hash::check('secure-admin-password', $admin->password))->toBeTrue();
});

it('promotes an existing user and preserves credentials when seeded repeatedly', function () {
    $user = User::factory()->create(['email' => 'admin@example.com', 'is_admin' => false]);
    $originalPassword = $user->password;
    config(['auth.admin_password' => null]);

    $this->seed(AdminUserSeeder::class);
    $this->seed(AdminUserSeeder::class);

    expect($user->refresh())->is_admin->toBeTrue()->password->toBe($originalPassword);
    expect(User::count())->toBe(1);
});

it('requires at least twelve password characters for a new administrator', function (?string $password) {
    config(['auth.admin_password' => $password]);

    expect(fn () => $this->seed(AdminUserSeeder::class))
        ->toThrow(RuntimeException::class, 'Set ADMIN_PASSWORD to at least 12 characters before seeding a new administrator.');

    $this->assertDatabaseCount('users', 0);
})->with([null, '', 'short']);
