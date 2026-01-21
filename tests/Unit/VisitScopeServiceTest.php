<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\User;
use App\Models\Visit;
use App\Services\VisitScopeService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitScopeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected VisitScopeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->service = new VisitScopeService();
    }

    public function test_sales_representative_can_only_see_their_own_visits()
    {
        $sr = User::factory()->create();
        $sr->assignRole('SalesRepresentative');

        $otherUser = User::factory()->create();

        $srVisit = Visit::factory()->create(['user_id' => $sr->id]);
        $otherVisit = Visit::factory()->create(['user_id' => $otherUser->id]);

        $results = $this->service->getVisitQuery($sr)->get();

        $this->assertTrue($results->contains($srVisit));
        $this->assertFalse($results->contains($otherVisit));
    }

    public function test_manager_can_see_their_own_and_subordinates_visits()
    {
        $manager = User::factory()->create();
        $manager->assignRole('AreaSalesManager');

        $subordinate = User::factory()->create(['manager_id' => $manager->id]);
        $subordinate->assignRole('SalesRepresentative');

        $grandSubordinate = User::factory()->create(['manager_id' => $subordinate->id]);
        $grandSubordinate->assignRole('SalesRepresentative');

        $otherUser = User::factory()->create();

        $managerVisit = Visit::factory()->create(['user_id' => $manager->id]);
        $subVisit = Visit::factory()->create(['user_id' => $subordinate->id]);
        $grandSubVisit = Visit::factory()->create(['user_id' => $grandSubordinate->id]);
        $otherVisit = Visit::factory()->create(['user_id' => $otherUser->id]);

        $results = $this->service->getVisitQuery($manager)->get();

        $this->assertTrue($results->contains($managerVisit));
        $this->assertTrue($results->contains($subVisit));
        $this->assertTrue($results->contains($grandSubVisit));
        $this->assertFalse($results->contains($otherVisit));
    }

    public function test_head_can_see_all_visits()
    {
        $head = User::factory()->create();
        $head->assignRole('Head');

        $sr = User::factory()->create();
        $visit = Visit::factory()->create(['user_id' => $sr->id]);

        $results = $this->service->getVisitQuery($head)->get();

        $this->assertTrue($results->contains($visit));
    }

    public function test_it_calculates_stats_correctly()
    {
        $user = User::factory()->create();
        $user->assignRole('Head');

        // Current month
        Visit::factory()->count(3)->create([
            'user_id' => $user->id,
            'visit_started_at' => now(),
        ]);

        // Last month
        Visit::factory()->count(2)->create([
            'user_id' => $user->id,
            'visit_started_at' => now()->subMonth(),
        ]);

        $stats = $this->service->getVisitStats($user);

        $this->assertEquals(5, $stats['total']);
        $this->assertEquals(3, $stats['monthly']);
        $this->assertEquals(50.0, $stats['growth']); // (3-2)/2 * 100 = 50%
    }

    public function test_it_calculates_customer_stats_correctly()
    {
        $user = User::factory()->create();
        $user->assignRole('Head');
        $customer = Customer::factory()->create();

        $lastMonth = now()->subMonth();
        Visit::factory()->create([
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'visit_started_at' => $lastMonth,
        ]);

        $today = now();
        Visit::factory()->create([
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'visit_started_at' => $today,
        ]);

        $stats = $this->service->getCustomerVisitStats($user, $customer->id);

        $this->assertEquals(2, $stats['total']);
        $this->assertEquals($today->toDateTimeString(), $stats['last_visit_date']->toDateTimeString());
    }
}
