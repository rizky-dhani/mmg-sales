<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\Distributor;
use App\Models\Item;
use App\Models\Order;
use App\Models\Position;
use App\Models\Principal;
use App\Models\Project;
use App\Models\SalesType;
use App\Models\Segment;
use App\Models\SubSegment;
use App\Models\Territory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PrincipalSeeder::class,
            PrincipalProductSeeder::class,
            ProductSeeder::class,
        ]);

        $users = User::all();
        $departments = Department::all();
        $positions = Position::all();

        $principals = Principal::all();
        $items = Item::all();

        $territories = Territory::factory(15)->create();
        $segments = Segment::factory(4)->create();
        $subSegments = collect();
        foreach ($segments as $segment) {
            $subSegments = $subSegments->merge(SubSegment::factory(2)->create(['segment_id' => $segment->id]));
        }

        $customerGroups = CustomerGroup::factory(3)->create();
        $salesTypes = SalesType::factory(3)->create();
        $distributors = Distributor::factory(3)->create();

        $customers = collect();
        foreach ($users->where('email', '!=', 'superadmin@medquest.co.id') as $user) {
            $customers = $customers->merge(Customer::factory(5)->create([
                'assigned_to' => $user->id,
            ]));
        }

        foreach ($customers as $customer) {
            Contact::factory(rand(1, 3))->create(['customer_id' => $customer->id]);
        }

        $projectTitles = [
            'Procurement of Laboratory Equipment',
            'Research Lab Equipment Upgrade',
            'Diagnostic Reagent Supply Q1',
            'Microbiology Lab Expansion Project',
            'Tender for Cold Storage Equipment',
            'Pathology Lab Automation',
            'Molecular Diagnostics Setup',
            'Quality Control Testing Equipment',
        ];

        for ($i = 0; $i < 40; $i++) {
            $user = $users->random();
            $customer = $customers->random();
            $status = fake()->randomElement(['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost']);

            $ageDays = match (true) {
                $i < 10 => rand(1, 7),
                $i < 25 => rand(15, 35),
                default => rand(45, 90),
            };

            $createdAt = Carbon::now()->subDays($ageDays)->subHours(rand(1, 23));
            $convertedAt = null;

            if (in_array($status, ['won', 'lost'])) {
                $convertedAt = (clone $createdAt)->addDays(rand(5, $ageDays));
            }

            $estimatedValue = $items->random()->unit_price * rand(1, 5);

            $project = Project::create([
                'title' => fake()->randomElement($projectTitles).' - '.$customer->name,
                'customer_name' => $customer->name,
                'contact_person' => $customer->contacts->first()->name ?? fake()->name(),
                'email' => $customer->email,
                'phone' => $customer->phone,
                'status' => $status,
                'source' => fake()->randomElement(['website', 'referral', 'trade_show', 'partner']),
                'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
                'estimated_value' => $estimatedValue,
                'estimated_revenue' => $estimatedValue * rand(80, 100) / 100,
                'estimated_completion_date' => (clone $createdAt)->addDays(rand(45, 120)),
                'notes' => fake()->paragraph(),
                'customer_id' => $customer->id,
                'assigned_to' => $user->id,
                'created_at' => $createdAt,
                'updated_at' => $convertedAt ?? $createdAt->addDays(rand(1, 5)),
                'converted_at' => $convertedAt,
                'position' => Str::random(10),
            ]);

            $activityCount = rand(2, 6);
            for ($j = 0; $j < $activityCount; $j++) {
                Activity::factory()->create([
                    'project_id' => $project->id,
                    'user_id' => $project->assigned_to,
                    'performed_at' => (clone $createdAt)->addDays(rand(0, $ageDays)),
                ]);
            }
        }

        $wonProjects = Project::where('status', 'won')->get();
        foreach ($wonProjects as $project) {
            $item = $items->random();
            $subSegment = $subSegments->random();
            $srUser = $users->where('id', $project->assigned_to)->first() ?? $users->random();

            Order::factory()->create([
                'project_id' => $project->id,
                'end_customer_id' => $project->customer_id,
                'original_customer_id' => $project->customer_id,
                'customer_group_id' => $customerGroups->random()->id,
                'item_id' => $item->id,
                'principal_id' => $item->principal_id,
                'segment_id' => $subSegment->segment_id,
                'sub_segment_id' => $subSegment->id,
                'area_city_id' => $territories->random()->id,
                'sales_type_id' => $salesTypes->random()->id,
                'distributor_id' => $distributors->random()->id,
                'department_id' => $srUser->department_id ?? $departments->random()->id,
                'sr_position_id' => $srUser->position_id ?? $positions->where('code', 'SR')->first()->id,
                'spv_position_id' => $positions->where('code', 'SPV')->first()->id,
                'rsm_asm_position_id' => $positions->where('code', 'RSM')->first()->id,
                'head_position_id' => $positions->where('code', 'HEAD')->first()->id,
                'created_by' => $srUser->id,
                'order_date' => $project->converted_at ?? Carbon::now(),
                'total_amount' => $project->estimated_value,
            ]);
        }

        $activityPurposes = [
            'Initial Introduction',
            'Technical Presentation',
            'Relationship Maintenance',
            'Product Demo Follow-up',
            'Price Negotiation',
            'Stock Availability Check',
            'After-sales Support Visit',
        ];

        foreach ($customers->random(min(30, $customers->count())) as $customer) {
            $user = $users->random();
            $startedAt = Carbon::now()->subDays(rand(1, 60))->subHours(rand(1, 12));
            $endedAt = (clone $startedAt)->addMinutes(rand(30, 120));

            Activity::create([
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'contact_id' => $customer->contacts->first()->id ?? null,
                'type' => fake()->randomElement(['In-person Meeting', 'Online Meeting', 'Phone Call', 'Messaging']),
                'visit_started_at' => $startedAt,
                'visit_ended_at' => $endedAt,
                'performed_at' => $startedAt,
                'location' => fake()->randomElement(['Hospital Office', 'Doctor\'s Lounge', 'Hospital Lobby', 'Cafe nearby']),
                'purpose' => fake()->randomElement($activityPurposes),
                'subject' => fake()->randomElement($activityPurposes),
                'expectations' => 'Establish relationship and understand their current equipment needs.',
                'targets' => 'Identify at least 2 potential capital equipment requirements for this year.',
                'description' => 'Productive meeting. The lab manager seems interested in the new equipment.',
                'stakeholder_feedback' => fake()->boolean(70) ? 'Good progress. Follow up with a formal proposal.' : null,
                'is_worth_keeping' => fake()->boolean(80),
                'confidence_level' => fake()->numberBetween(0, 100),
            ]);
        }
    }
}
