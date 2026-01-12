<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Segment;
use App\Models\SubSegment;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use App\Filament\Resources\Segments\SegmentResource;
use App\Filament\Resources\SubSegments\SubSegmentResource;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->superAdmin = User::factory()->create();
    $this->superAdmin->assignRole('Super Admin');
    
    $this->salesRep = User::factory()->create();
    $this->salesRep->assignRole('SalesRepresentative');
});

it('allows Super Admin to view segments and subsegments', function () {
    actingAs($this->superAdmin);
    
    expect($this->superAdmin->can('viewAny', Segment::class))->toBeTrue();
    expect($this->superAdmin->can('viewAny', SubSegment::class))->toBeTrue();
    
    expect(SegmentResource::shouldRegisterNavigation())->toBeTrue();
    expect(SubSegmentResource::shouldRegisterNavigation())->toBeTrue();
});

it('denies Sales Representative from viewing segments and subsegments', function () {
    actingAs($this->salesRep);
    
    expect($this->salesRep->can('viewAny', Segment::class))->toBeFalse();
    expect($this->salesRep->can('viewAny', SubSegment::class))->toBeFalse();
    
    expect(SegmentResource::shouldRegisterNavigation())->toBeFalse();
    expect(SubSegmentResource::shouldRegisterNavigation())->toBeFalse();
});
