<?php

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

it('logs an update event on order change', function (): void
{
    Activity::query()->delete();

    $order = Order::factory()->create();
    $order->update(['total_amount' => 999]);

    $log = Activity::query()
        ->where('subject_type', Order::class)
        ->where('subject_id', $order->id)
        ->where('event', 'updated')
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->event)->toBe('updated')
        ->and((int) $log->attribute_changes->get('attributes')['total_amount'])->toBe(999);
});
