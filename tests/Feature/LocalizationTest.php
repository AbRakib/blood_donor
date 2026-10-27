<?php

use App\Models\User;

it('renders the public page and login in the selected language', function (string $locale, string $heading) {
    $this->withSession(['locale' => $locale])->get(route('home'))
        ->assertOk()->assertSee('lang="'.$locale.'"', false)->assertSee($heading);

    $this->get(route('login'))->assertOk()->assertSee('lang="'.$locale.'"', false);
})->with([
    ['en', 'Manda Blood Donation Program'],
    ['bn', 'মান্দা রক্ত দান কর্মসূচি'],
]);

it('saves the language and uses it on subsequent pages', function () {
    $this->from(route('home'))->post(route('locale.update', 'bn'))
        ->assertRedirect(route('home'))->assertSessionHas('locale', 'bn');

    $this->get(route('login'))->assertOk()->assertSee('আবারও স্বাগতম!');

    $this->post(route('locale.update', 'en'))->assertSessionHas('locale', 'en');
    $this->get(route('login'))->assertOk()->assertSee('Welcome back!');
});

it('rejects unsupported languages without replacing the preference', function () {
    $this->withSession(['locale' => 'bn'])->post('/language/fr')
        ->assertNotFound()->assertSessionHas('locale', 'bn');
});

it('uses English when the stored locale is unsupported', function () {
    $this->withSession(['locale' => '../../invalid'])->get(route('login'))
        ->assertOk()->assertSee('lang="en"', false)->assertSee('Welcome back!');
});

it('renders the dashboard and its javascript translations in the selected language', function (string $locale, string $heading) {
    $this->actingAs(User::factory()->make(['is_admin' => true]))
        ->withSession(['locale' => $locale])->get(route('dashboard'))
        ->assertOk()->assertSee('lang="'.$locale.'"', false)
        ->assertSee($heading)->assertSee('window.donationTranslations', false);
})->with([
    ['en', 'Dashboard overview'],
    ['bn', 'ড্যাশবোর্ড ওভারভিউ'],
]);

it('returns Bangla validation errors for Bangla sessions', function () {
    $this->withSession(['locale' => 'bn'])->postJson(route('login.store'), [])
        ->assertUnprocessable()->assertJsonPath('errors.email.0', 'ইমেইল দিতে হবে।');
});

it('preserves the selected language after logout', function () {
    $this->actingAs(User::factory()->make(['is_admin' => true, 'remember_token' => null]))
        ->withSession(['locale' => 'bn'])->post(route('logout'))
        ->assertRedirect(route('login'))->assertSessionHas('locale', 'bn');
});
