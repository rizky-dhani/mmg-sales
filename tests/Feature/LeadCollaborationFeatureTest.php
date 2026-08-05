<?php

use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\Leads\Pages\ViewLead;
use App\Models\Lead;
use App\Models\User;
use App\Policies\ActivityPolicy;
use App\Policies\LeadPolicy;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

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

    expect($policy->createForLead($collaborator, $lead))->toBeTrue();
});

it('non-collaborator cannot create activity on lead', function () {
    $creator = User::factory()->create();
    $otherUser = User::factory()->create();

    $lead = Lead::factory()->create([
        'created_by' => $creator->id,
    ]);

    $this->actingAs($otherUser);

    $policy = new ActivityPolicy;

    expect($policy->createForLead($otherUser, $lead))->toBeFalse();
});

it('renders Sales Rep collaborators once on lead view page', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $creator = User::factory()->create();
    $creator->assignRole('Super Admin');

    $collaborators = collect(['Alice Rep', 'Bob Rep', 'Carol Rep'])
        ->map(fn (string $name) => User::factory()->create(['name' => $name]));

    $lead = Lead::factory()->create([
        'created_by' => $creator->id,
        'customer_id' => null,
    ]);

    $lead->collaborators()->attach(
        $collaborators->pluck('id')->all(),
        ['added_by' => $creator->id],
    );

    actingAs($creator);

    $html = Livewire::test(ViewLead::class, ['record' => $lead->getRouteKey()])
        ->assertSuccessful()
        ->html();

    // Each collaborator name must appear exactly once — a duplicated entry
    // would render the whole list once per collaborator.
    foreach ($collaborators as $collaborator) {
        expect(substr_count($html, $collaborator->name))->toBe(1);
    }
});
