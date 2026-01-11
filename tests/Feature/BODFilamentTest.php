<?php

use App\Models\User;
use App\Models\Lead;
use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\Leads\Pages\ListLeads;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->bodUser = User::factory()->create();
    $this->bodUser->assignRole('Board of Director');
});

it('allows BOD to access lead list but forbids create and edit pages', function () {
    $lead = Lead::factory()->create();

    actingAs($this->bodUser);

    // Can access list
    $this->get(LeadResource::getUrl('index'))
        ->assertSuccessful();

    // Forbidden from create
    $this->get(LeadResource::getUrl('create'))
        ->assertForbidden();

    // Forbidden from edit
    $this->get(LeadResource::getUrl('edit', ['record' => $lead]))
        ->assertForbidden();
});

it('hides create and edit actions from lead table for BOD but shows view', function () {
    $lead = Lead::factory()->create();

    actingAs($this->bodUser);

    Livewire::test(ListLeads::class)
        ->assertActionHidden('create')
        ->assertTableActionHidden('edit', $lead)
        ->assertTableActionVisible('view', $lead);
});
