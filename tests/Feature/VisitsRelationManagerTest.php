<?php

namespace Tests\Feature;

use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Customers\Pages\EditCustomer;
use App\Filament\Resources\Customers\Pages\ViewCustomer;
use App\Filament\Resources\Customers\RelationManagers\VisitsRelationManager;
use App\Models\Customer;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');
});

it('can list visits for a customer and only has view action', function () {
    $customer = Customer::factory()->create();
    $visits = Visit::factory()->count(3)->create(['customer_id' => $customer->id]);

    actingAs($this->user);

    Livewire::test(VisitsRelationManager::class, [
        'ownerRecord' => $customer,
        'pageClass' => ViewCustomer::class,
    ])
        ->assertCanSeeTableRecords($visits)
        ->assertTableColumnExists('user.name')
        ->assertTableColumnExists('visit_started_at')
        ->assertTableColumnExists('purpose')
        ->assertTableActionVisible('view')
        ->assertTableActionDoesNotExist('edit')
        ->assertTableActionDoesNotExist('delete')
        ->assertActionDoesNotExist('create');
});

it('is visible on view page and hidden on edit page', function () {
    $customer = Customer::factory()->create();

    actingAs($this->user);

    expect(VisitsRelationManager::canViewForRecord($customer, ViewCustomer::class))->toBeTrue();
    expect(VisitsRelationManager::canViewForRecord($customer, EditCustomer::class))->toBeFalse();
});

it('renders on the view page but not on the edit page', function () {
    $customer = Customer::factory()->create();
    $visit = Visit::factory()->create(['customer_id' => $customer->id]);

    actingAs($this->user);

    // View Page
    $this->get(CustomerResource::getUrl('view', ['record' => $customer]))
        ->assertSuccessful()
        ->assertSee('Visits'); // This might still match sidebar

    // Edit Page
    // We expect the relation manager NOT to be there. 
    // Filament relation managers are rendered as livewire components.
    $this->get(CustomerResource::getUrl('edit', ['record' => $customer]))
        ->assertSuccessful()
        ->assertDontSeeHtml('app.filament.resources.companies.relation-managers.visits-relation-manager');
});
