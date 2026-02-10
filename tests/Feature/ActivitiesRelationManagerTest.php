<?php

namespace Tests\Feature;

use App\Filament\Resources\Customers\Pages\ViewCustomer;
use App\Filament\Resources\Customers\RelationManagers\ActivitiesRelationManager;
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

it('can list activities for a customer', function () {
    $customer = Customer::factory()->create();
    $activities = Activity::factory()->count(3)->create(['customer_id' => $customer->id]);

    livewire(ActivitiesRelationManager::class, [
        'ownerRecord' => $customer,
        'pageClass' => ViewCustomer::class,
    ])
        ->assertCanSeeTableRecords($activities)
        ->assertTableColumnExists('performed_at');
});
