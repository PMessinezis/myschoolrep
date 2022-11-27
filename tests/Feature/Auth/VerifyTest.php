<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

use App\Providers\RouteServiceProvider;
use Illuminate\Support\Carbon;

test('can view verification page', function()
{
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    Auth::login($user);

    $this->get(route('verification.notice'))
        ->assertSuccessful()
        ->assertSeeLivewire('auth.verify');
});

test('can resend verification email', function()
{
    $user = User::factory()->create();

    Livewire::actingAs($user);

    Livewire::test('auth.verify')
        ->call('resend')
        ->assertEmitted('resent');
});

test('can verify', function()
{
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    Auth::login($user);

    $url = URL::temporarySignedRoute('verification.verify', Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)), [
        'id' => $user->getKey(),
        'hash' => sha1($user->getEmailForVerification()),
    ]);

    $this->get($url)
        ->assertRedirect(route('home'));

    $this->assertTrue($user->hasVerifiedEmail());
});