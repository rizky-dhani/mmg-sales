<?php

namespace Tests\Feature;

use App\Filament\Resources\Visits\VisitResource;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');
});

it('can render the visit view page with infolist data', function () {
    $visit = Visit::factory()->create([
        'purpose' => 'Test Visit Purpose',
        'expectations' => 'Test Expectations',
        'targets' => 'Test Targets',
        'summary_notes' => 'Test Summary Notes',
    ]);

    actingAs($this->user);

    $this->get(VisitResource::getUrl('view', ['record' => $visit]))
        ->assertSuccessful()
        ->assertSee('Visit Logistics')
        ->assertSee('Strategic Intent')
        ->assertSee('Visit Outcome')
        ->assertSee('Stakeholder Review')
        ->assertSee('Test Visit Purpose')
        ->assertSee('Test Expectations')
        ->assertSee('Test Targets')
        ->assertSee('Test Summary Notes');
});

it('hides stakeholder review from non-authorized roles', function () {
    $visit = Visit::factory()->create();
    $salesRep = User::factory()->create();
    $salesRep->assignRole('SalesRepresentative');

    actingAs($salesRep);

    $this->get(VisitResource::getUrl('view', ['record' => $visit]))
        ->assertSuccessful()
        ->assertDontSee('Stakeholder Review');
});