<?php

namespace Tests\Feature;

use App\Filament\Resources\Activities\ActivityResource;
use App\Models\Activity;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;

uses(RefreshDatabase::class);

beforeEach(function () {
    seed(RolesAndPermissionsSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');
    actingAs($this->user);
});

it('can render the activity view page with infolist data', function () {
    $activity = Activity::factory()->create([
        'subject' => 'Test Interaction Subject',
        'type' => 'In-person Meeting',
    ]);

    $this->get(ActivityResource::getUrl('view', ['record' => $activity]))
        ->assertSuccessful()
        ->assertSee('Core Information')
        ->assertSee('Interaction Details')
        ->assertSee('Test Interaction Subject');
});
