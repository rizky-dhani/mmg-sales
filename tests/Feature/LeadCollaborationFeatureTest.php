<?php

use App\Models\Lead;
use App\Models\User;
use App\Policies\ActivityPolicy;
use App\Policies\LeadPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creator can add collaborator to lead', function () {
    $creator = User::factory()->create();
    $collaborator = User::factory()->create();

    $lead = Lead::factory()->create([
        'created_by' => $creator->id,
    ]);

    $this->actingAs($creator);

    $lead->collaborators()->attach($collaborator->id, ['added_by' => $creator->id]);

    expect($lead->collaborators)->toHaveCount(1);
});

it('non-creator cannot add collaborator to lead', function () {
    $creator = User::factory()->create();
    $otherUser = User::factory()->create();
    $collaborator = User::factory()->create();

    $lead = Lead::factory()->create([
        'created_by' => $creator->id,
    ]);

    $this->actingAs($otherUser);

    $policy = new LeadPolicy;

    expect($policy->addCollaborator($otherUser, $lead))->toBeFalse();
});

it('creator can remove collaborator from lead', function () {
    $creator = User::factory()->create();
    $collaborator = User::factory()->create();

    $lead = Lead::factory()->create([
        'created_by' => $creator->id,
    ]);

    $lead->collaborators()->attach($collaborator->id, ['added_by' => $creator->id]);

    $this->actingAs($creator);

    $lead->collaborators()->detach($collaborator->id);

    expect($lead->collaborators)->toHaveCount(0);
});

it('collaborator can create activity on lead', function () {
    $creator = User::factory()->create();
    $collaborator = User::factory()->create();

    $lead = Lead::factory()->create([
        'created_by' => $creator->id,
    ]);

    $lead->collaborators()->attach($collaborator->id, ['added_by' => $creator->id]);

    $this->actingAs($collaborator);

    $policy = new ActivityPolicy;

    expect($policy->createForProject($collaborator, $lead))->toBeTrue();
});

it('non-collaborator cannot create activity on lead', function () {
    $creator = User::factory()->create();
    $otherUser = User::factory()->create();

    $lead = Lead::factory()->create([
        'created_by' => $creator->id,
    ]);

    $this->actingAs($otherUser);

    $policy = new ActivityPolicy;

    expect($policy->createForProject($otherUser, $lead))->toBeFalse();
});
