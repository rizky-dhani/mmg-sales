<?php

use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

it('can open and submit the project checklist action', function () {
    Role::create(['name' => 'Super Admin']);
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    $this->actingAs($user);

    $m1 = Milestone::create(['name' => 'Budget Confirmed', 'weight' => 20]);
    $project = Project::factory()->create(['confidence_level' => 0]);

    livewire(ListProjects::class)
        ->callTableAction('updateChecklist', $project, data: [
            'milestones' => [
                [
                    'milestone_id' => $m1->id,
                    'is_completed' => true,
                    'notes' => 'Confirmed with CFO',
                ],
            ],
        ])
        ->assertHasNoTableActionErrors();

    $project->refresh();
    expect($project->confidence_level)->toBe(20)
        ->and($project->milestones)->toHaveCount(1)
        ->and($project->milestones->first()->pivot->is_completed)->toBeTrue()
        ->and($project->milestones->first()->pivot->notes)->toBe('Confirmed with CFO');
});
