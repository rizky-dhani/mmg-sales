<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\Distributor;
use App\Models\Item;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Position;
use App\Models\Principal;
use App\Models\Product;
use App\Models\SalesType;
use App\Models\Segment;
use App\Models\SubSegment;
use App\Models\Territory;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get existing base data
        $users = User::all();
        $departments = Department::all();
        $positions = Position::all();

        // 2. Generate Lookup/Master Data Pool
        $territories = Territory::factory(10)->create();
        $principals = Principal::factory(5)->create();
        $segments = Segment::factory(3)->create();
        $subSegments = collect();
        foreach ($segments as $segment) {
            $subSegments = $subSegments->merge(SubSegment::factory(2)->create(['segment_id' => $segment->id]));
        }

        $customerGroups = CustomerGroup::factory(3)->create();
        $salesTypes = SalesType::factory(2)->create();
        $distributors = Distributor::factory(3)->create();

        // 3. Generate Entities (Products, Items, Customers)
        $products = Product::factory(15)->create();

        $items = collect();
        foreach ($principals as $principal) {
            $items = $items->merge(Item::factory(4)->create(['principal_id' => $principal->id]));
        }

        $customers = collect();
        foreach ($users->where('email', '!=', 'superadmin@medquest.co.id') as $user) {
            $customers = $customers->merge(Customer::factory(5)->create([
                'assigned_to' => $user->id,
            ]));
        }

        // Generate Contacts for each customer
        foreach ($customers as $customer) {
            Contact::factory(rand(1, 3))->create(['customer_id' => $customer->id]);
        }

        // 4. Generate Leads (picking from existing pools)
        $leads = Lead::factory(20)->create([
            'assigned_to' => fn () => $users->random()->id,
            'customer_id' => fn () => fake()->boolean(40) ? $customers->random()->id : null,
        ]);

        // Generate Activities for each lead
        foreach ($leads as $lead) {
            Activity::factory(rand(2, 5))->create([
                'lead_id' => $lead->id,
                'user_id' => $lead->assigned_to,
            ]);
        }

        // 5. Generate Orders (the complex part)
        for ($i = 0; $i < 50; $i++) {
            $customer = $customers->random();
            $item = $items->random();
            $subSegment = $subSegments->random();
            $srUser = $users->where('email', '!=', 'superadmin@medquest.co.id')->random();

            Order::factory()->create([
                'end_customer_id' => $customer->id,
                'original_customer_id' => $customer->id,
                'customer_group_id' => $customerGroups->random()->id,
                'item_id' => $item->id,
                'principal_id' => $item->principal_id,
                'segment_id' => $subSegment->segment_id,
                'sub_segment_id' => $subSegment->id,
                'area_city_id' => $territories->where('type', 'city')->random()->id ?? $territories->random()->id,
                'sales_type_id' => $salesTypes->random()->id,
                'distributor_id' => $distributors->random()->id,
                'department_id' => $srUser->department_id ?? $departments->random()->id,
                'sr_position_id' => $srUser->position_id ?? $positions->where('code', 'SR')->first()->id,
                // Picking managers based on common logic
                'spv_position_id' => $positions->where('code', 'SPV')->first()->id,
                'rsm_asm_position_id' => $positions->where('code', 'RSM')->first()->id,
                'head_position_id' => $positions->where('code', 'HEAD')->first()->id,
                'created_by' => $srUser->id,
            ]);
        }
    }
}
