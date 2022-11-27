<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('an authenticated user can log out', function()
{
    $user = User::factory()->create();
    $this->be($user);

    $this->post(route('logout'))
        ->assertRedirect(route('home'));

    $this->assertFalse(Auth::check());
});

test('an unauthenticated user can not log out', function()
{
    $this->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertFalse(Auth::check());
});
