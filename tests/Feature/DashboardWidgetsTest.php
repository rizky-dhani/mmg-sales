<?php

namespace Tests\Feature;

use App\Filament\Widgets\ActivityStatsWidget;
use App\Filament\Widgets\RecentActivitiesWidget;
use App\Models\Activity;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

it('activity stats widget displays correct data', function () {
    Activity::factory()->count(5)->create([
        'user_id' => $this->user->id,
        'performed_at' => now(),
        'type' => 'In-person Meeting',
    ]);

    livewire(ActivityStatsWidget::class)
        ->assertSee('Monthly Interactions');
});

it('recent activities widget has view action', function () {
    $activity = Activity::factory()->create(['subject' => 'Unique Subject X']);

    livewire(RecentActivitiesWidget::class)
        ->assertCanSeeTableRecords([$activity])
        ->assertTableActionExists('view');
});
