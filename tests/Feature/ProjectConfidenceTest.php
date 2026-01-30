<?php

use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('calculates confidence level correctly when milestones are completed', function () {
    // 1. Setup Milestones
    $m1 = Milestone::create(['name' => 'Budget Confirmed', 'weight' => 20]);
    $m2 = Milestone::create(['name' => 'Decision Maker Met', 'weight' => 15]);
    $m3 = Milestone::create(['name' => 'Need Validated', 'weight' => 20]);

    // 2. Create a Project
    $project = Project::factory()->create([
        'customer_name' => 'Test Customer',
        'confidence_level' => 0,
    ]);

    // 3. Attach milestones via pivot
    $project->milestones()->attach($m1->id, ['is_completed' => true]);
    $project->milestones()->attach($m2->id, ['is_completed' => false]);
    $project->milestones()->attach($m3->id, ['is_completed' => true]);

    // 4. Verify confidence level (expected 20 + 20 = 40)
    // Note: The ProjectMilestone pivot model has a saved event that calls updateConfidenceLevel()
    $project->refresh();

    expect($project->confidence_level)->toBe(40);
});

it('updates confidence level when a milestone is marked as completed', function () {
    $m1 = Milestone::create(['name' => 'Budget Confirmed', 'weight' => 20]);
    $project = Project::factory()->create(['confidence_level' => 0]);

    $project->milestones()->attach($m1->id, ['is_completed' => false]);
    $project->refresh();
    expect($project->confidence_level)->toBe(0);

    // Update pivot
    $project->milestones()->updateExistingPivot($m1->id, ['is_completed' => true]);

    $project->refresh();
    expect($project->confidence_level)->toBe(20);
});

it('updates confidence level when a milestone is detached', function () {
    $m1 = Milestone::create(['name' => 'Budget Confirmed', 'weight' => 20]);
    $project = Project::factory()->create();

    $project->milestones()->attach($m1->id, ['is_completed' => true]);
    $project->refresh();
    expect($project->confidence_level)->toBe(20);

    // Detach
    $project->milestones()->detach($m1->id);

    $project->refresh();
    expect($project->confidence_level)->toBe(0);
});
