<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creator can add collaborator to project', function () {
    $creator = User::factory()->create();
    $collaborator = User::factory()->create();

    $project = Project::factory()->create([
        'created_by' => $creator->id,
    ]);

    $this->actingAs($creator);

    $project->collaborators()->attach($collaborator->id, ['added_by' => $creator->id]);

    expect($project->collaborators)->toHaveCount(1);
});

it('non-creator cannot add collaborator to project', function () {
    $creator = User::factory()->create();
    $otherUser = User::factory()->create();
    $collaborator = User::factory()->create();

    $project = Project::factory()->create([
        'created_by' => $creator->id,
    ]);

    $this->actingAs($otherUser);

    $policy = new \App\Policies\ProjectPolicy;

    expect($policy->addCollaborator($otherUser, $project))->toBeFalse();
});

it('creator can remove collaborator from project', function () {
    $creator = User::factory()->create();
    $collaborator = User::factory()->create();

    $project = Project::factory()->create([
        'created_by' => $creator->id,
    ]);

    $project->collaborators()->attach($collaborator->id, ['added_by' => $creator->id]);

    $this->actingAs($creator);

    $project->collaborators()->detach($collaborator->id);

    expect($project->collaborators)->toHaveCount(0);
});

it('collaborator can create activity on project', function () {
    $creator = User::factory()->create();
    $collaborator = User::factory()->create();

    $project = Project::factory()->create([
        'created_by' => $creator->id,
    ]);

    $project->collaborators()->attach($collaborator->id, ['added_by' => $creator->id]);

    $this->actingAs($collaborator);

    $policy = new \App\Policies\ActivityPolicy;

    expect($policy->createForProject($collaborator, $project))->toBeTrue();
});

it('non-collaborator cannot create activity on project', function () {
    $creator = User::factory()->create();
    $otherUser = User::factory()->create();

    $project = Project::factory()->create([
        'created_by' => $creator->id,
    ]);

    $this->actingAs($otherUser);

    $policy = new \App\Policies\ActivityPolicy;

    expect($policy->createForProject($otherUser, $project))->toBeFalse();
});
