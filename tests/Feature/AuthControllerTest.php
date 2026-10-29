<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withSession(['locale' => 'bn']);
});

it('renders the admin login form for guests', function () {
    $this->get(route('login'))->assertOk()->assertViewIs('admin.auth.login')->assertSee('লগইন করুন');
});

it('redirects dashboard guests to login', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('forbids the dashboard for nonadministrators', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)->get(route('dashboard'))->assertForbidden();
});

it('redirects authenticated administrators away from login', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->get(route('login'))->assertRedirect(route('dashboard'));
});

it('rejects incorrect passwords without authenticating', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->from(route('login'))->post(route('login.store'), [
        'email' => $admin->email, 'password' => 'incorrect',
    ])->assertRedirect(route('login'))->assertSessionHasErrors(['email' => 'ইমেইল অথবা পাসওয়ার্ড সঠিক নয়।']);

    $this->assertGuest();
});

it('requires credentials to log in', function () {
    $this->post(route('login.store'), [])->assertSessionHasErrors(['email', 'password']);

    $this->assertGuest();
});

it('returns administrators to their intended dashboard after login', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->withSession(['url.intended' => route('dashboard')])->post(route('login.store'), [
        'email' => $admin->email, 'password' => 'password',
    ])->assertRedirect(route('dashboard'))->assertSessionHasNoErrors();

    $this->assertAuthenticatedAs($admin);
});

it('logs administrators out and clears their session', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->withSession(['private_data' => 'secret'])->post(route('logout'))
        ->assertRedirect(route('login'))->assertSessionMissing('private_data')
        ->assertSessionHas('status', 'আপনি সফলভাবে লগআউট করেছেন।');

    $this->assertGuest();
});

it('rate limits repeated login attempts', function () {
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->post(route('login.store'), [])
            ->assertSessionHasErrors('email');
    }

    $this->post(route('login.store'), [])
        ->assertTooManyRequests();

    $this->assertGuest();
});
