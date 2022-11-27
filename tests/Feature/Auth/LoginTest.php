<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

test('can view login page', function()
{
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSeeLivewire('auth.login');
});

test('is redirected if already logged in', function()
{
    $user = User::factory()->create();

    $this->be($user);

    $this->get(route('login'))
        ->assertRedirect(route('home'));
});

test('a user can login', function()
{
    $user = User::factory()->create(['password' => Hash::make('password')]);

    livewire('auth.login')
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('authenticate');

    $this->assertAuthenticatedAs($user);
});

test('is redirected to the home page after login', function()
{
    $user = User::factory()->create(['password' => Hash::make('password')]);

    livewire('auth.login')
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('authenticate')
        ->assertRedirect(route('home'));
});

test('email is required', function()
{
    $user = User::factory()->create(['password' => Hash::make('password')]);

    livewire('auth.login')
        ->set('password', 'password')
        ->call('authenticate')
        ->assertHasErrors(['email' => 'required']);
});

test('email must be valid email', function()
{
    $user = User::factory()->create(['password' => Hash::make('password')]);

    livewire('auth.login')
        ->set('email', 'invalid-email')
        ->set('password', 'password')
        ->call('authenticate')
        ->assertHasErrors(['email' => 'email']);
});

test('password is required', function()
{
    $user = User::factory()->create(['password' => Hash::make('password')]);

    livewire('auth.login')
        ->set('email', $user->email)
        ->call('authenticate')
        ->assertHasErrors(['password' => 'required']);
});

test('bad login attempt shows message', function()
{
    $user = User::factory()->create();

    livewire('auth.login')
        ->set('email', $user->email)
        ->set('password', 'bad-password')
        ->call('authenticate')
        ->assertHasErrors('email');

    $this->assertFalse(Auth::check());
});