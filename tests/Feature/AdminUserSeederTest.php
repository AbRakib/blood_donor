<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(LazilyRefreshDatabase::class);

it('creates the administrator with the default password', function () {
    config(['auth.admin_password' => 'secure-admin-password']);

    $this->seed(AdminUserSeeder::class);

    $admin = User::where('email', 'admin@gmail.com')->sole();

    expect($admin)->name->toBe('Administrator')->is_admin->toBeTrue();
    expect(Hash::check('123456', $admin->password))->toBeTrue();
});

it('promotes an existing user and preserves credentials when seeded repeatedly', function () {
    $user = User::factory()->create(['email' => 'admin@gmail.com', 'is_admin' => false]);
    $originalPassword = $user->password;
    config(['auth.admin_password' => null]);

    $this->seed(AdminUserSeeder::class);
    $this->seed(AdminUserSeeder::class);

    expect($user->refresh())->is_admin->toBeTrue()->password->toBe($originalPassword);
    expect(User::count())->toBe(1);
});

it('creates the administrator without requiring a configured password', function (?string $password) {
    config(['auth.admin_password' => $password]);

    $this->seed(AdminUserSeeder::class);

    $admin = User::where('email', 'admin@gmail.com')->sole();

    expect($admin)->is_admin->toBeTrue();
    expect(Hash::check('123456', $admin->password))->toBeTrue();
})->with([null, '', 'short']);
