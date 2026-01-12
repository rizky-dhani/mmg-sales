<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Distributor;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use App\Filament\Resources\Distributors\DistributorResource;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->superAdmin = User::factory()->create();
    $this->superAdmin->assignRole('Super Admin');
    
    $this->salesRep = User::factory()->create();
    $this->salesRep->assignRole('SalesRepresentative');
});

it('allows Super Admin to view distributors', function () {
    actingAs($this->superAdmin);
    
    expect($this->superAdmin->can('viewAny', Distributor::class))->toBeTrue();
    expect(DistributorResource::shouldRegisterNavigation())->toBeTrue();
});

it('denies Sales Representative from viewing distributors', function () {
    actingAs($this->salesRep);
    
    expect($this->salesRep->can('viewAny', Distributor::class))->toBeFalse();
    expect(DistributorResource::shouldRegisterNavigation())->toBeFalse();
});
