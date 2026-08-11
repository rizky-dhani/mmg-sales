<?php

use App\Models\Activity;
use App\Models\Customer;
use App\Models\User;
use App\Services\QuickActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates an activity with auth user', function (): void
{
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    $activity = app(QuickActivityService::class)->log([
        'customer_id' => $customer->id,
        'type' => 'call',
        'subject' => 'Follow-up call',
        'notes' => 'Spoke with lab head',
    ], $user->id);

    expect($activity)->toBeInstanceOf(Activity::class)
        ->and($activity->user_id)->toBe($user->id)
        ->and($activity->customer_id)->toBe($customer->id)
        ->and($activity->type)->toBe('call')
        ->and($activity->activity_code)->not->toBeNull();
});

it('throws when customer does not exist', function (): void
{
    $user = User::factory()->create();

    app(QuickActivityService::class)->log([
        'customer_id' => 999999,
        'type' => 'call',
        'subject' => 'x',
    ], $user->id);
})->throws(Illuminate\Database\Eloquent\ModelNotFoundException::class);
