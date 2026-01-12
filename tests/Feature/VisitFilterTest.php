<?php

namespace Tests\Feature;

use App\Filament\Resources\Visits\Pages\ListVisits;
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

it('can filter visits by company', function () {
    $companyA = Company::factory()->create(['facility_name' => 'Company A']);
    $companyB = Company::factory()->create(['facility_name' => 'Company B']);

    $visitA = Visit::factory()->create(['company_id' => $companyA->id, 'purpose' => 'Visit A']);
    $visitB = Visit::factory()->create(['company_id' => $companyB->id, 'purpose' => 'Visit B']);

    actingAs($this->user);

    Livewire::test(ListVisits::class)
        ->assertCanSeeTableRecords([$visitA, $visitB])
        ->filterTable('company_id', $companyA->id)
        ->assertCanSeeTableRecords([$visitA])
        ->assertCanNotSeeTableRecords([$visitB]);
});