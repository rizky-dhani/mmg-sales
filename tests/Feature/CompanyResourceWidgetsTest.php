<?php

namespace Tests\Feature;

use App\Filament\Widgets\CustomerRecentVisitsWidget;
use App\Filament\Widgets\CustomerVisitStatsWidget;
use App\Models\Customer;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerResourceWidgetsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('Super Admin');
        $this->customer = Customer::factory()->create();
    }

    public function test_customer_visit_stats_widget_displays_correct_data()
    {
        Visit::factory()->count(3)->create([
            'customer_id' => $this->customer->id,
            'visit_started_at' => now(),
        ]);

        Livewire::actingAs($this->user)
            ->test(CustomerVisitStatsWidget::class, ['record' => $this->customer])
            ->assertSee('Total Customer Visits')
            ->assertSee('3');
    }

    public function test_customer_recent_visits_widget_displays_only_this_customer_data()
    {
        $visitThisCustomer = Visit::factory()->create([
            'customer_id' => $this->customer->id,
            'purpose' => 'Correct Customer Visit'
        ]);
        
        $otherCustomer = Customer::factory()->create();
        $visitOtherCustomer = Visit::factory()->create([
            'customer_id' => $otherCustomer->id,
            'purpose' => 'Wrong Customer Visit'
        ]);

        Livewire::actingAs($this->user)
            ->test(CustomerRecentVisitsWidget::class, ['record' => $this->customer])
            ->assertCanSeeTableRecords([$visitThisCustomer])
            ->assertCanNotSeeTableRecords([$visitOtherCustomer]);
    }
}
