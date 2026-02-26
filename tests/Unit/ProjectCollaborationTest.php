<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('has creator relationship', function () {
    $creator = User::factory()->create();
    $project = Project::factory()->create([
        'created_by' => $creator->id,
    ]);

    expect($project->creator)->toBeInstanceOf(User::class);
    expect($project->creator->id)->toBe($creator->id);
});

it('has collaborators relationship', function () {
    $creator = User::factory()->create();
    $project = Project::factory()->create([
        'created_by' => $creator->id,
    ]);
    $collaborator = User::factory()->create();

    $project->collaborators()->attach($collaborator->id, ['added_by' => $creator->id]);

    expect($project->collaborators)->toHaveCount(1);
    expect($project->collaborators->first()->id)->toBe($collaborator->id);
});

it('auto sets created_by when creating project with auth', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $project = Project::factory()->create([
        'created_by' => null,
    ]);

    expect($project->created_by)->toBe($user->id);
});

it('does not overwrite existing created_by', function () {
    $creator = User::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($otherUser);

    $project = Project::factory()->create([
        'created_by' => $creator->id,
    ]);

    expect($project->created_by)->toBe($creator->id);
});
