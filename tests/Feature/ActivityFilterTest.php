<?php

namespace Tests\Feature;

use App\Filament\Resources\Activities\Pages\ListActivities;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    seed(RolesAndPermissionsSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');
    actingAs($this->user);
});

it('can filter activities by customer', function () {
    $customerA = Customer::factory()->create(['name' => 'Hospital A']);
    $customerB = Customer::factory()->create(['name' => 'Hospital B']);

    $activityA = Activity::factory()->create(['customer_id' => $customerA->id, 'subject' => 'Activity A']);
    $activityB = Activity::factory()->create(['customer_id' => $customerB->id, 'subject' => 'Activity B']);

    livewire(ListActivities::class)
        ->assertCanSeeTableRecords([$activityA, $activityB])
        ->filterTable('customer', $customerA->id)
        ->assertCanSeeTableRecords([$activityA])
        ->assertCanNotSeeTableRecords([$activityB]);
});
