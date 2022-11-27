<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registration_page_contains_livewire_component', function()
{
    $this->get(route('register'))
        ->assertSuccessful()
        ->assertSeeLivewire('auth.register');
});

test('is_redirected_if_already_logged_in', function()
{
    $user = User::factory()->create();

    $this->be($user);

    $this->get(route('register'))
        ->assertRedirect(route('home'));
});

test('a_user_can_register', function()
{
    Event::fake();

    Livewire::test('auth.register')
        ->set('name', 'Tall Stack')
        ->set('email', 'tallstack@example.com')
        ->set('password', 'password')
        ->set('passwordConfirmation', 'password')
        ->call('register')
        ->assertRedirect(route('home'));

    $this->assertTrue(User::whereEmail('tallstack@example.com')->exists());
    $this->assertEquals('tallstack@example.com', Auth::user()->email);

    Event::assertDispatched(Registered::class);
});

test('name_is_required', function()
{
    Livewire::test('auth.register')
        ->set('name', '')
        ->call('register')
        ->assertHasErrors(['name' => 'required']);
});

test('email_is_required', function()
{
    Livewire::test('auth.register')
        ->set('email', '')
        ->call('register')
        ->assertHasErrors(['email' => 'required']);
});

test('email_is_valid_email', function()
{
    Livewire::test('auth.register')
        ->set('email', 'tallstack')
        ->call('register')
        ->assertHasErrors(['email' => 'email']);
});

test('email_hasnt_been_taken_already', function()
{
    User::factory()->create(['email' => 'tallstack@example.com']);

    Livewire::test('auth.register')
        ->set('email', 'tallstack@example.com')
        ->call('register')
        ->assertHasErrors(['email' => 'unique']);
});

test('see_email_hasnt_already_been_taken_validation_message_as_user_types', function()
{
    User::factory()->create(['email' => 'tallstack@example.com']);

    Livewire::test('auth.register')
        ->set('email', 'smallstack@gmail.com')
        ->assertHasNoErrors()
        ->set('email', 'tallstack@example.com')
        ->call('register')
        ->assertHasErrors(['email' => 'unique']);
});

test('password_is_required', function()
{
    Livewire::test('auth.register')
        ->set('password', '')
        ->set('passwordConfirmation', 'password')
        ->call('register')
        ->assertHasErrors(['password' => 'required']);
});

test('password_is_minimum_of_eight_characters', function()
{
    Livewire::test('auth.register')
        ->set('password', 'secret')
        ->set('passwordConfirmation', 'secret')
        ->call('register')
        ->assertHasErrors(['password' => 'min']);
});

test('password_matches_password_confirmation', function()
{
    Livewire::test('auth.register')
        ->set('email', 'tallstack@example.com')
        ->set('password', 'password')
        ->set('passwordConfirmation', 'not-password')
        ->call('register')
        ->assertHasErrors(['password' => 'same']);
});