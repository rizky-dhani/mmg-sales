<?php

use App\Models\Distributor;
use Database\Seeders\DistributorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('populates MMG and MJG distributors correctly', function () {
    $this->seed(DistributorSeeder::class);

    expect(Distributor::count())->toBe(2);
    
    $this->assertDatabaseHas('distributors', [
        'name' => 'MMG',
        'code' => 'MMG',
    ]);
    
    $this->assertDatabaseHas('distributors', [
        'name' => 'MJG',
        'code' => 'MJG',
    ]);
});

it('is idempotent', function () {
    $this->seed(DistributorSeeder::class);
    $count = Distributor::count();

    $this->seed(DistributorSeeder::class);

    expect(Distributor::count())->toBe($count);
});
