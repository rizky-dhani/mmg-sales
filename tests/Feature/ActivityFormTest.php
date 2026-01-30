<?php

namespace Tests\Feature;

use App\Filament\Resources\Activities\Pages\CreateActivity;
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
