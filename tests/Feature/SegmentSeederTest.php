<?php

use App\Models\Segment;
use App\Models\SubSegment;
use Database\Seeders\SegmentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('populates segments and sub segments correctly', function () {
    $this->seed(SegmentSeeder::class);

    // 10 Segments defined in seeder
    expect(Segment::count())->toBe(10);

    // 17 SubSegments defined in seeder
    expect(SubSegment::count())->toBe(17);

    // Verify some specific data
    $this->assertDatabaseHas('segments', ['name' => 'INDUSTRY', 'code' => 'industry']);
    $this->assertDatabaseHas('sub_segments', ['name' => 'PHARMA & VACCINES', 'code' => 'industry-pharma-vaccines']);

    // Verify relationship
    $industry = Segment::where('name', 'INDUSTRY')->first();
    expect($industry->subSegments()->count())->toBe(4);
});

it('is idempotent', function () {
    $this->seed(SegmentSeeder::class);
    $count = Segment::count();
    $subCount = SubSegment::count();

    $this->seed(SegmentSeeder::class);

    expect(Segment::count())->toBe($count);
    expect(SubSegment::count())->toBe($subCount);
});
