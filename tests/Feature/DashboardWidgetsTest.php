<?php

namespace Tests\Feature;

use App\Filament\Widgets\RecentVisitsWidget;
use App\Filament\Widgets\SalesRepLeaderboardWidget;
use App\Filament\Widgets\VisitStatsWidget;
use App\Models\Company;
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
        $sr = User::factory()->create(['name' => 'John Rep']);
        $company = Company::factory()->create(['facility_name' => 'Test Clinic']);
        
        Visit::factory()->count(5)->create([
            'user_id' => $sr->id,
            'company_id' => $company->id,
            'visit_started_at' => now()
        ]);

        Livewire::actingAs($this->user)
            ->test(VisitStatsWidget::class)
            ->assertSee('Monthly Visits')
            ->assertSee('5')
            ->assertSee('Top Co. Engagement')
            ->assertSee('John Rep');
    }

    public function test_recent_visits_widget_has_view_action()
    {
        $visit = Visit::factory()->create(['purpose' => 'Unique Purpose X']);

        Livewire::actingAs($this->user)
            ->test(RecentVisitsWidget::class)
            ->assertCanSeeTableRecords([$visit])
            ->assertTableActionExists('view');
    }

    public function test_sales_rep_leaderboard_displays_scoped_data_with_company()
    {
        $sr = User::factory()->create(['name' => 'John Doe']);
        $company = Company::factory()->create(['facility_name' => 'Med Center']);
        
        Visit::factory()->count(3)->create([
            'user_id' => $sr->id,
            'company_id' => $company->id
        ]);

        Livewire::actingAs($this->user)
            ->test(SalesRepLeaderboardWidget::class)
            ->assertSee('John Doe')
            ->assertSee('Med Center')
            ->assertSee('3');
    }
}