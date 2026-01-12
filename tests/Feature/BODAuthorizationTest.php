<?php

use App\Models\User;
use App\Models\Lead;
use App\Models\Company;
use App\Models\Order;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->bodUser = User::factory()->create();
    $this->bodUser->assignRole('Board of Director');
});

it('allows BOD to view resources but denies create/update/delete', function () {
    $lead = Lead::factory()->create();
    $company = Company::factory()->create();
    
    // Test Lead Authorization
    expect($this->bodUser->can('viewAny', Lead::class))->toBeTrue();
    expect($this->bodUser->can('view', $lead))->toBeTrue();
    expect($this->bodUser->can('create', Lead::class))->toBeFalse();
    expect($this->bodUser->can('update', $lead))->toBeFalse();
    expect($this->bodUser->can('delete', $lead))->toBeFalse();

    // Test Company Authorization
    expect($this->bodUser->can('viewAny', Company::class))->toBeTrue();
    expect($this->bodUser->can('view', $company))->toBeTrue();
    expect($this->bodUser->can('create', Company::class))->toBeFalse();
    expect($this->bodUser->can('update', $company))->toBeFalse();
    expect($this->bodUser->can('delete', $company))->toBeFalse();

    // Test Order Authorization
    expect($this->bodUser->can('viewAny', Order::class))->toBeTrue();
    expect($this->bodUser->can('create', Order::class))->toBeFalse();
});