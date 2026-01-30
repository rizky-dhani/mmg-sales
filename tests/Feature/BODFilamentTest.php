<?php

use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->bodUser = User::factory()->create();
    $this->bodUser->assignRole('Board of Director');
});

it('allows BOD to access project list but forbids create and edit pages', function () {
    $project = Project::factory()->create();

    actingAs($this->bodUser);

    // Can access list
    $this->get(ProjectResource::getUrl('index'))
        ->assertSuccessful();

    // Forbidden from create
    $this->get(ProjectResource::getUrl('create'))
        ->assertForbidden();

    // Forbidden from edit
    $this->get(ProjectResource::getUrl('edit', ['record' => $project]))
        ->assertForbidden();
});

it('hides create and edit actions from project table for BOD but shows view', function () {
    $project = Project::factory()->create();

    actingAs($this->bodUser);

    Livewire::test(ListProjects::class)
        ->assertActionHidden('create')
        ->assertTableActionHidden('edit', $project)
        ->assertTableActionVisible('view', $project);
});
