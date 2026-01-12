<?php

namespace Tests\Feature;

use App\Filament\Resources\Companies\CompanyResource;
use App\Filament\Resources\Companies\Pages\EditCompany;
use App\Filament\Resources\Companies\Pages\ViewCompany;
use App\Filament\Resources\Companies\RelationManagers\VisitsRelationManager;
use App\Models\Company;
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

it('can list visits for a company and only has view action', function () {
    $company = Company::factory()->create();
    $visits = Visit::factory()->count(3)->create(['company_id' => $company->id]);

    actingAs($this->user);

    Livewire::test(VisitsRelationManager::class, [
        'ownerRecord' => $company,
        'pageClass' => ViewCompany::class,
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
    $company = Company::factory()->create();

    actingAs($this->user);

    expect(VisitsRelationManager::canViewForRecord($company, ViewCompany::class))->toBeTrue();
    expect(VisitsRelationManager::canViewForRecord($company, EditCompany::class))->toBeFalse();
});

it('renders on the view page but not on the edit page', function () {
    $company = Company::factory()->create();
    $visit = Visit::factory()->create(['company_id' => $company->id]);

    actingAs($this->user);

    // View Page
    $this->get(CompanyResource::getUrl('view', ['record' => $company]))
        ->assertSuccessful()
        ->assertSee('Visits'); // This might still match sidebar

    // Edit Page
    // We expect the relation manager NOT to be there. 
    // Filament relation managers are rendered as livewire components.
    $this->get(CompanyResource::getUrl('edit', ['record' => $company]))
        ->assertSuccessful()
        ->assertDontSeeHtml('app.filament.resources.companies.relation-managers.visits-relation-manager');
});
