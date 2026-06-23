<?php

use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can save and retrieve estimation fields', function () {
    $lead = Lead::factory()->create([
        'estimated_revenue' => 150000000.50,
        'estimated_completion_date' => '2026-12-31',
    ]);

    expect($lead->estimated_revenue)->toBe('150000000.50')
        ->and($lead->estimated_completion_date->format('Y-m-d'))->toBe('2026-12-31');
});

it('can update estimation fields', function () {
    $lead = Lead::factory()->create();

    $lead->update([
        'estimated_revenue' => 250000000.00,
        'estimated_completion_date' => '2027-06-30',
    ]);

    $lead->refresh();

    expect($lead->estimated_revenue)->toBe('250000000.00')
        ->and($lead->estimated_completion_date->format('Y-m-d'))->toBe('2027-06-30');
});
