<?php

namespace Tests\Feature;

use App\Filament\Widgets\RecentVisitsWidget;
use App\Filament\Widgets\SalesRepLeaderboardWidget;
use App\Filament\Widgets\VisitStatsWidget;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardWidgetsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('Super Admin');
    }

    public function test_visit_stats_widget_displays_correct_data()
    {
        Visit::factory()->count(5)->create(['visit_started_at' => now()]);

        Livewire::actingAs($this->user)
            ->test(VisitStatsWidget::class)
            ->assertSee('Total Visits')
            ->assertSee('5');
    }

    public function test_recent_visits_widget_displays_scoped_data()
    {
        $visit = Visit::factory()->create(['purpose' => 'Unique Purpose X']);

        Livewire::actingAs($this->user)
            ->test(RecentVisitsWidget::class)
            ->assertCanSeeTableRecords([$visit]);
    }

    public function test_sales_rep_leaderboard_displays_scoped_data()
    {
        $sr = User::factory()->create(['name' => 'John Doe']);
        Visit::factory()->count(3)->create(['user_id' => $sr->id]);

        Livewire::actingAs($this->user)
            ->test(SalesRepLeaderboardWidget::class)
            ->assertSee('John Doe')
            ->assertSee('3');
    }
}
