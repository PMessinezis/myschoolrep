<?php

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

test('can view password reset page', function()
{
    $user = User::factory()->create();

    $token = Str::random(16);

    DB::table('password_resets')->insert([
        'email' => $user->email,
        'token' => Hash::make($token),
        'created_at' => Carbon::now(),
    ]);

    $this->get(route('password.reset', [
        'email' => $user->email,
        'token' => $token,
    ]))
        ->assertSuccessful()
        ->assertSee($user->email)
        ->assertSeeLivewire('auth.passwords.reset');
});

test('can reset password', function()
{
    $user = User::factory()->create();

    $token = Str::random(16);

    DB::table('password_resets')->insert([
        'email' => $user->email,
        'token' => Hash::make($token),
        'created_at' => Carbon::now(),
    ]);

    livewire('auth.passwords.reset', [
        'token' => $token,
    ])
        ->set('email', $user->email)
        ->set('password', 'new-password')
        ->set('passwordConfirmation', 'new-password')
        ->call('resetPassword');

    $this->assertTrue(Auth::attempt([
        'email' => $user->email,
        'password' => 'new-password',
    ]));
});

test('token is required', function()
{
    livewire('auth.passwords.reset', [
        'token' => null,
    ])
        ->call('resetPassword')
        ->assertHasErrors(['token' => 'required']);
});

test('email is required', function()
{
    livewire('auth.passwords.reset', [
        'token' => Str::random(16),
    ])
        ->set('email', null)
        ->call('resetPassword')
        ->assertHasErrors(['email' => 'required']);
});

test('email is valid email', function()
{
    livewire('auth.passwords.reset', [
        'token' => Str::random(16),
    ])
        ->set('email', 'email')
        ->call('resetPassword')
        ->assertHasErrors(['email' => 'email']);
});

test('password is required', function()
{
    livewire('auth.passwords.reset', [
        'token' => Str::random(16),
    ])
        ->set('password', '')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'required']);
});

test('password is minimum of eight characters', function()
{
    livewire('auth.passwords.reset', [
        'token' => Str::random(16),
    ])
        ->set('password', 'secret')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'min']);
});

test('password matches password confirmation', function()
{
    livewire('auth.passwords.reset', [
        'token' => Str::random(16),
    ])
        ->set('password', 'new-password')
        ->set('passwordConfirmation', 'not-new-password')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'same']);
});
