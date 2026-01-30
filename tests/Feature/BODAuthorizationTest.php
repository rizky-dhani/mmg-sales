<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->bodUser = User::factory()->create();
    $this->bodUser->assignRole('Board of Director');
});

it('allows BOD to view resources but denies create/update/delete', function () {
    $project = Project::factory()->create();
    $customer = Customer::factory()->create();

    // Test Project Authorization
    expect($this->bodUser->can('viewAny', Project::class))->toBeTrue();
    expect($this->bodUser->can('view', $project))->toBeTrue();
    expect($this->bodUser->can('create', Project::class))->toBeFalse();
    expect($this->bodUser->can('update', $project))->toBeFalse();
    expect($this->bodUser->can('delete', $project))->toBeFalse();

    // Test Customer Authorization
    expect($this->bodUser->can('viewAny', Customer::class))->toBeTrue();
    expect($this->bodUser->can('view', $customer))->toBeTrue();
    expect($this->bodUser->can('create', Customer::class))->toBeFalse();
    expect($this->bodUser->can('update', $customer))->toBeFalse();
    expect($this->bodUser->can('delete', $customer))->toBeFalse();

    // Test Order Authorization
    expect($this->bodUser->can('viewAny', Order::class))->toBeTrue();
    expect($this->bodUser->can('create', Order::class))->toBeFalse();
});
