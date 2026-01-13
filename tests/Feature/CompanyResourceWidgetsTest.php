<?php

namespace Tests\Feature;

use App\Filament\Widgets\CompanyRecentVisitsWidget;
use App\Filament\Widgets\CompanyVisitStatsWidget;
use App\Models\Company;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CompanyResourceWidgetsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('Super Admin');
        $this->company = Company::factory()->create();
    }

    public function test_company_visit_stats_widget_displays_correct_data()
    {
        Visit::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'visit_started_at' => now(),
        ]);

        Livewire::actingAs($this->user)
            ->test(CompanyVisitStatsWidget::class, ['record' => $this->company])
            ->assertSee('Total Company Visits')
            ->assertSee('3');
    }

    public function test_company_recent_visits_widget_displays_only_this_company_data()
    {
        $visitThisCompany = Visit::factory()->create([
            'company_id' => $this->company->id,
            'purpose' => 'Correct Company Visit'
        ]);
        
        $otherCompany = Company::factory()->create();
        $visitOtherCompany = Visit::factory()->create([
            'company_id' => $otherCompany->id,
            'purpose' => 'Wrong Company Visit'
        ]);

        Livewire::actingAs($this->user)
            ->test(CompanyRecentVisitsWidget::class, ['record' => $this->company])
            ->assertCanSeeTableRecords([$visitThisCompany])
            ->assertCanNotSeeTableRecords([$visitOtherCompany]);
    }
}
