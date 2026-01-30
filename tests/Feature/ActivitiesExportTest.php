<?php

namespace Tests\Feature;

use App\Exports\ActivitiesExport;
use App\Exports\ActivitiesMultiSheetExport;
use App\Filament\Resources\Activities\Pages\ListActivities;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
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

it('can export activities to excel', function () {
    Excel::fake();

    Activity::factory()->count(5)->create();

    livewire(ListActivities::class)
        ->callTableAction('exportExcel');

    Excel::assertDownloaded('activities-export-'.now()->format('Y-m-d').'.xlsx', function (ActivitiesExport $export) {
        return true;
    });
});

it('can export filtered activities to excel', function () {
    Excel::fake();

    $customer1 = Customer::factory()->create();
    $customer2 = Customer::factory()->create();

    Activity::factory()->count(3)->create(['customer_id' => $customer1->id]);
    Activity::factory()->count(2)->create(['customer_id' => $customer2->id]);

    livewire(ListActivities::class)
        ->filterTable('customer', $customer1->id)
        ->callTableAction('exportExcel');

    Excel::assertDownloaded('activities-export-'.now()->format('Y-m-d').'.xlsx', function (ActivitiesExport $export) {
        return true;
    });
});