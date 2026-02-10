<?php

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can save and retrieve estimation fields', function () {
    $project = Project::factory()->create([
        'estimated_revenue' => 150000000.50,
        'estimated_completion_date' => '2026-12-31',
    ]);

    expect($project->estimated_revenue)->toBe('150000000.50')
        ->and($project->estimated_completion_date->format('Y-m-d'))->toBe('2026-12-31');
});

it('can update estimation fields', function () {
    $project = Project::factory()->create();

    $project->update([
        'estimated_revenue' => 250000000.00,
        'estimated_completion_date' => '2027-06-30',
    ]);

    $project->refresh();

    expect($project->estimated_revenue)->toBe('250000000.00')
        ->and($project->estimated_completion_date->format('Y-m-d'))->toBe('2027-06-30');
});
