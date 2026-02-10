<?php

use App\Filament\Widgets\UpcomingProjectsWidget;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    seed(RolesAndPermissionsSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    actingAs($user);
});

it('displays projects nearing completion sorted by date', function () {
    $p1 = Project::factory()->create([
        'title' => 'Project 1',
        'estimated_completion_date' => now()->addDays(5),
    ]);

    $p2 = Project::factory()->create([
        'title' => 'Project 2',
        'estimated_completion_date' => now()->addDays(2),
    ]);

    $p3 = Project::factory()->create([
        'title' => 'Project 3',
        'estimated_completion_date' => now()->subDays(1), // Should be excluded by >= now()
    ]);

    livewire(UpcomingProjectsWidget::class)
        ->assertCanSeeTableRecords([$p2, $p1])
        ->assertCanNotSeeTableRecords([$p3])
        ->assertTableActionExists('view');
});

it('can navigate to view project page from record url', function () {
    $project = Project::factory()->create([
        'estimated_completion_date' => now()->addDays(1),
    ]);

    livewire(UpcomingProjectsWidget::class)
        ->assertTableActionHasUrl('view', \App\Filament\Resources\Projects\ProjectResource::getUrl('view', ['record' => $project]), $project);
});
