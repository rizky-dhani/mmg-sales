<?php

namespace Tests\Feature;

use App\Filament\Resources\Activities\Pages\CreateActivity;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\seed;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    seed(RolesAndPermissionsSeeder::class);
});

it('can create a basic activity', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    actingAs($user);

    livewire(CreateActivity::class)
        ->set('data.type', 'Call')
        ->set('data.subject', 'Basic Call')
        ->set('data.performed_at', now()->format('Y-m-d H:i:s'))
        ->set('data.user_id', $user->id)
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas('activities', [
        'type' => 'Call',
        'subject' => 'Basic Call',
    ]);
});

it('can create an activity with a contact', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    actingAs($user);

    $customer = Customer::factory()->create();
    $contact = Contact::factory()->create(['customer_id' => $customer->id]);

    livewire(CreateActivity::class)
        ->set('data.customer_id', $customer->id)
        ->set('data.contact_id', $contact->id)
        ->set('data.type', 'Call')
        ->set('data.subject', 'Call with Contact')
        ->set('data.performed_at', now()->format('Y-m-d H:i:s'))
        ->set('data.user_id', $user->id)
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas('activities', [
        'type' => 'Call',
        'customer_id' => $customer->id,
        'contact_id' => $contact->id,
    ]);
});

it('can create activity with date within 3 days backdate', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    actingAs($user);

    $dateWithin3Days = now()->subDays(2)->format('Y-m-d H:i:s');

    livewire(CreateActivity::class)
        ->set('data.type', 'Call')
        ->set('data.subject', 'Backdate Test')
        ->set('data.performed_at', $dateWithin3Days)
        ->set('data.user_id', $user->id)
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas('activities', [
        'type' => 'Call',
        'subject' => 'Backdate Test',
    ]);
});

it('cannot create activity with date older than 3 days', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    actingAs($user);

    $dateOlderThan3Days = now()->subDays(4)->format('Y-m-d H:i:s');

    expect(function () use ($user, $dateOlderThan3Days) {
        livewire(CreateActivity::class)
            ->set('data.type', 'Call')
            ->set('data.subject', 'Old Activity')
            ->set('data.performed_at', $dateOlderThan3Days)
            ->set('data.user_id', $user->id)
            ->call('create');
    })->toThrow(\InvalidArgumentException::class, 'Activity date cannot be more than 3 days in the past.');
});

it('can create activity for today', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    actingAs($user);

    $today = now()->format('Y-m-d H:i:s');

    livewire(CreateActivity::class)
        ->set('data.type', 'Call')
        ->set('data.subject', 'Today Activity')
        ->set('data.performed_at', $today)
        ->set('data.user_id', $user->id)
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas('activities', [
        'type' => 'Call',
        'subject' => 'Today Activity',
    ]);
});

it('can create activity for future date', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    actingAs($user);

    $futureDate = now()->addDays(2)->format('Y-m-d H:i:s');

    livewire(CreateActivity::class)
        ->set('data.type', 'Call')
        ->set('data.subject', 'Future Activity')
        ->set('data.performed_at', $futureDate)
        ->set('data.user_id', $user->id)
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas('activities', [
        'type' => 'Call',
        'subject' => 'Future Activity',
    ]);
});
