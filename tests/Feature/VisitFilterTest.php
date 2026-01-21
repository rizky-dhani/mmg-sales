<?php

namespace Tests\Feature;

use App\Filament\Resources\Visits\Pages\ListVisits;
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

it('can filter visits by customer', function () {
    $customerA = Customer::factory()->create(['facility_name' => 'Customer A']);
    $customerB = Customer::factory()->create(['facility_name' => 'Customer B']);

    $visitA = Visit::factory()->create(['customer_id' => $customerA->id, 'purpose' => 'Visit A']);
    $visitB = Visit::factory()->create(['customer_id' => $customerB->id, 'purpose' => 'Visit B']);

    actingAs($this->user);

    Livewire::test(ListVisits::class)
        ->assertCanSeeTableRecords([$visitA, $visitB])
        ->filterTable('customer_id', $customerA->id)
        ->assertCanSeeTableRecords([$visitA])
        ->assertCanNotSeeTableRecords([$visitB]);
});