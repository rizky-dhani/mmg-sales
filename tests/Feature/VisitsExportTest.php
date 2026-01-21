<?php

use App\Exports\VisitsExport;
use App\Exports\VisitsMultiSheetExport;
use App\Filament\Resources\Visits\Pages\ListVisits;
use App\Models\Customer;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

it('can export visits to excel', function () {
    Excel::fake();

    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    $this->actingAs($user);

    Visit::factory()->count(5)->create();

    Livewire::test(ListVisits::class)
        ->callTableAction('export', null, ['type' => 'standard']);

    Excel::assertDownloaded('visits-export-'.now()->format('Y-m-d').'.xlsx', function (VisitsExport $export) {
        return $export->query()->count() === 5;
    });
});

it('can export filtered visits to excel', function () {
    Excel::fake();

    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    $this->actingAs($user);

    $customer1 = Customer::factory()->create();
    $customer2 = Customer::factory()->create();

    Visit::factory()->count(3)->create(['customer_id' => $customer1->id]);
    Visit::factory()->count(2)->create(['customer_id' => $customer2->id]);

    Livewire::test(ListVisits::class)
        ->filterTable('customer_id', $customer1->id)
        ->callTableAction('export', null, ['type' => 'standard']);

    Excel::assertDownloaded('visits-export-'.now()->format('Y-m-d').'.xlsx', function (VisitsExport $export) {
        return $export->query()->count() === 3;
    });
});

it('can export visits to excel grouped by representative', function () {
    Excel::fake();

    $user1 = User::factory()->create();
    $user1->assignRole('Super Admin');
    $user2 = User::factory()->create();

    $this->actingAs($user1);

    Visit::factory()->count(3)->create(['user_id' => $user1->id]);
    Visit::factory()->count(2)->create(['user_id' => $user2->id]);

    Livewire::test(ListVisits::class)
        ->mountTableAction('export')
        ->fillTableActionForm(['type' => 'by_rep'])
        ->callMountedTableAction();

    Excel::assertDownloaded('visits-export-'.now()->format('Y-m-d').'.xlsx', function (VisitsMultiSheetExport $export) {
        $sheets = $export->sheets();

        return count($sheets) === 2;
    });
});

it('can export visits to excel grouped by customer', function () {
    Excel::fake();

    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    $this->actingAs($user);

    $customer1 = Customer::factory()->create();
    $customer2 = Customer::factory()->create();
    $customer3 = Customer::factory()->create();

    Visit::factory()->create(['customer_id' => $customer1->id]);
    Visit::factory()->create(['customer_id' => $customer2->id]);
    Visit::factory()->create(['customer_id' => $customer3->id]);

    Livewire::test(ListVisits::class)
        ->mountTableAction('export')
        ->fillTableActionForm(['type' => 'by_customer'])
        ->callMountedTableAction();

    Excel::assertDownloaded('visits-export-'.now()->format('Y-m-d').'.xlsx', function (VisitsMultiSheetExport $export) {
        $sheets = $export->sheets();

        return count($sheets) === 3;
    });
});
