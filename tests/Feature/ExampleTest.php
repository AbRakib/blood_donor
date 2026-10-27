<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200)->assertViewIs('home');
});

test('the dashboard renders its blade view', function () {
    $this->get('/dashboard')->assertStatus(200)->assertViewIs('dashboard');
});
