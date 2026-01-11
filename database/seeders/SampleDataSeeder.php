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
use App\Models\SalesType;
use App\Models\Segment;
use App\Models\SubSegment;
use App\Models\Territory;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

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

        // 2. Generate Realistic Principals
        $principalsData = [
            ['name' => 'GE Healthcare', 'code' => 'GEH'],
            ['name' => 'Philips Healthcare', 'code' => 'PHL'],
            ['name' => 'Siemens Healthineers', 'code' => 'SIE'],
            ['name' => 'Roche Diagnostics', 'code' => 'RCH'],
            ['name' => 'Abbott Laboratories', 'code' => 'ABT'],
            ['name' => 'Medtronic', 'code' => 'MDT'],
            ['name' => 'Stryker', 'code' => 'STR'],
        ];

        $principals = collect();
        foreach ($principalsData as $p) {
            $principals->push(Principal::create(array_merge($p, [
                'description' => $p['name'].' Medical Equipment and Supplies',
                'contact_person' => fake('id_ID')->name(),
                'phone' => fake('id_ID')->phoneNumber(),
                'email' => strtolower($p['code']).'@'.strtolower(str_replace(' ', '', $p['name'])).'.com',
                'address' => fake('id_ID')->address(),
                'is_active' => true,
            ])));
        }

        // 3. Generate Realistic Items with balanced pricing
        $itemsData = [
            'GEH' => [
                ['name' => 'Revolution CT Scanner', 'price' => 12000000000, 'type' => 'Capital'],
                ['name' => 'Voluson S10 Ultrasound', 'price' => 1500000000, 'type' => 'Capital'],
                ['name' => 'Logiq E9 Probe', 'price' => 120000000, 'type' => 'Consumable'],
                ['name' => 'CT Contrast Media (Bulk)', 'price' => 15000000, 'type' => 'Consumable'],
            ],
            'PHL' => [
                ['name' => 'Azurion 7 Angiography', 'price' => 15000000000, 'type' => 'Capital'],
                ['name' => 'Affiniti 70 Ultrasound', 'price' => 1800000000, 'type' => 'Capital'],
                ['name' => 'ECG Electrodes (Case)', 'price' => 2500000, 'type' => 'Consumable'],
                ['name' => 'Patient Monitor Cables', 'price' => 4500000, 'type' => 'Consumable'],
            ],
            'RCH' => [
                ['name' => 'Cobas 6000 Analyzer', 'price' => 3500000000, 'type' => 'Capital'],
                ['name' => 'Cobas Reagent Pack (Chemistry)', 'price' => 25000000, 'type' => 'Consumable'],
                ['name' => 'Accu-Chek Test Strips (5000)', 'price' => 12000000, 'type' => 'Consumable'],
                ['name' => 'Elecsys Immunoassay Kit', 'price' => 18000000, 'type' => 'Consumable'],
            ],
            'ABT' => [
                ['name' => 'Alinity ci-series', 'price' => 4500000000, 'type' => 'Capital'],
                ['name' => 'i-STAT Handheld System', 'price' => 250000000, 'type' => 'Capital'],
                ['name' => 'Alinity Reagent Cartridge', 'price' => 15000000, 'type' => 'Consumable'],
                ['name' => 'Hepatitis B Assay Kit', 'price' => 8500000, 'type' => 'Consumable'],
            ],
            'MDT' => [
                ['name' => 'StealthStation S8 Navigation', 'price' => 6000000000, 'type' => 'Capital'],
                ['name' => 'Puritan Bennett 980 Ventilator', 'price' => 850000000, 'type' => 'Capital'],
                ['name' => 'Endotracheal Tubes (Bulk)', 'price' => 12000000, 'type' => 'Consumable'],
                ['name' => 'OxiMax Pulse Oximeter Sensor', 'price' => 1500000, 'type' => 'Consumable'],
            ],
        ];

        $items = collect();
        foreach ($itemsData as $code => $pItems) {
            $principal = $principals->where('code', $code)->first();
            if ($principal) {
                foreach ($pItems as $item) {
                    $items->push(Item::create([
                        'name' => $item['name'],
                        'principal_id' => $principal->id,
                        'internal_code' => $code.'-'.fake()->unique()->numerify('####'),
                        'principle_code' => $code.'-'.strtoupper(str()->random(5)),
                        'unit_price' => $item['price'],
                        'unit' => $item['type'] === 'Capital' ? 'Unit' : 'Pack',
                        'description' => $item['name'].' medical '.strtolower($item['type']),
                        'is_active' => true,
                    ]));
                }
            }
        }

        // 4. Master Data Pool
        $territories = Territory::factory(15)->create();
        $segments = Segment::factory(4)->create();
        $subSegments = collect();
        foreach ($segments as $segment) {
            $subSegments = $subSegments->merge(SubSegment::factory(2)->create(['segment_id' => $segment->id]));
        }

        $customerGroups = CustomerGroup::factory(3)->create();
        $salesTypes = SalesType::factory(3)->create();
        $distributors = Distributor::factory(3)->create();

        // 5. Customers
        $customers = collect();
        foreach ($users->where('email', '!=', 'superadmin@medquest.co.id') as $user) {
            $customers = $customers->merge(Customer::factory(5)->create([
                'assigned_to' => $user->id,
            ]));
        }

        foreach ($customers as $customer) {
            Contact::factory(rand(1, 3))->create(['customer_id' => $customer->id]);
        }

        // 6. Generate Realistic Leads with Aging
        $leadTitles = [
            'Procurement of Radiology Equipment',
            'Surgical Department Equipment Upgrade',
            'Diagnostic Lab Reagent Supply Q1',
            'Cardiology Unit Expansion Project',
            'Tender for Emergency Care Monitors',
            'ICU Ventilator Installation Phase 2',
            'Pathology Lab Automation',
            'Oncology Center Supply Contract',
        ];

        for ($i = 0; $i < 40; $i++) {
            $user = $users->random();
            $customer = $customers->random();
            $status = fake()->randomElement(['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost']);

            // Generate realistic aging: some fresh (1-7 days), some aging (15-30), some old (45+)
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

            $lead = Lead::create([
                'title' => fake()->randomElement($leadTitles).' - '.$customer->facility_name,
                'company_name' => $customer->facility_name,
                'contact_person' => $customer->contacts->first()->name ?? fake()->name(),
                'email' => $customer->email,
                'phone' => $customer->phone,
                'status' => $status,
                'source' => fake()->randomElement(['website', 'referral', 'trade_show', 'partner']),
                'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
                'estimated_value' => $items->random()->unit_price * rand(1, 5),
                'notes' => fake()->paragraph(),
                'customer_id' => $customer->id,
                'assigned_to' => $user->id,
                'created_at' => $createdAt,
                'updated_at' => $convertedAt ?? $createdAt->addDays(rand(1, 5)),
                'converted_at' => $convertedAt,
                'position' => str()->random(10),
            ]);

            // Generate Activities for each lead
            $activityCount = rand(2, 6);
            for ($j = 0; $j < $activityCount; $j++) {
                Activity::factory()->create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->assigned_to,
                    'performed_at' => (clone $createdAt)->addDays(rand(0, $ageDays)),
                ]);
            }
        }

        // 7. Generate Orders (Won Leads and historical data)
        $wonLeads = Lead::where('status', 'won')->get();
        foreach ($wonLeads as $lead) {
            $item = $items->random();
            $subSegment = $subSegments->random();
            $srUser = $users->where('id', $lead->assigned_to)->first() ?? $users->random();

            Order::factory()->create([
                'lead_id' => $lead->id,
                'end_customer_id' => $lead->customer_id,
                'original_customer_id' => $lead->customer_id,
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
                'order_date' => $lead->converted_at ?? Carbon::now(),
                'total_amount' => $lead->estimated_value,
            ]);
        }

        // 8. Generate Visits (Relationship building)
        $visitPurposes = [
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

            Visit::create([
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'contact_id' => $customer->contacts->first()->id ?? null,
                'visit_started_at' => $startedAt,
                'visit_ended_at' => $endedAt,
                'location' => fake()->randomElement(['Hospital Office', 'Doctor\'s Lounge', 'Hospital Lobby', 'Cafe nearby']),
                'purpose' => fake()->randomElement($visitPurposes),
                'expectations' => 'Establish relationship and understand their current equipment needs.',
                'targets' => 'Identify at least 2 potential capital equipment requirements for this year.',
                'summary_notes' => 'Productive meeting. The head of radiology seems interested in the new CT scanner.',
                'stakeholder_feedback' => fake()->boolean(70) ? 'Good progress. Follow up with a formal proposal.' : null,
                'is_worth_keeping' => fake()->boolean(80),
            ]);
        }
    }
}
