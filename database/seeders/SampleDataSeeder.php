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
    protected array $principalProducts = [];

    protected function loadPrincipalProducts(): void
    {
        $filePath = database_path('../principals_products.txt');
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $currentPrincipal = null;
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (str_starts_with($line, '- ')) {
                if ($currentPrincipal) {
                    $productName = trim(substr($line, 2));
                    $this->principalProducts[$currentPrincipal]['products'][] = $productName;
                }
            } else {
                $currentPrincipal = $line;
                $this->principalProducts[$currentPrincipal] = [
                    'code' => $this->generateCode($line),
                    'products' => [],
                ];
            }
        }
    }

    protected function generateCode(string $name): string
    {
        $words = preg_split('/\s+/', $name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 2).substr($words[1], 0, 1));
        }

        return strtoupper(substr($name, 0, 3));
    }

    protected function inferProductType(string $name): string
    {
        $capitalKeywords = [
            'chromatograph', 'hplc', 'spectrofotometer', 'uv vis', 'mass spectrometer',
            'freezer', 'refrigerator', 'centrifuge', 'laminar', 'cabinet', 'safety cabinet',
            'analyzer', 'vortex', 'stirrer', 'infinite', 'spark', 'clia',
        ];

        $nameLower = strtolower($name);

        foreach ($capitalKeywords as $keyword) {
            if (str_contains($nameLower, $keyword)) {
                return 'Capital';
            }
        }

        return 'Consumable';
    }

    protected function generatePrice(string $type, string $name): int
    {
        if ($type === 'Capital') {
            $nameLower = strtolower($name);

            if (str_contains($nameLower, 'chromatograph') || str_contains($nameLower, 'hplc')) {
                return rand(8000000000, 15000000000);
            }
            if (str_contains($nameLower, 'spectrofotometer') || str_contains($nameLower, 'spectrophotometer')) {
                return rand(500000000, 2000000000);
            }
            if (str_contains($nameLower, 'mass spectrometer')) {
                return rand(10000000000, 20000000000);
            }
            if (str_contains($nameLower, 'ultra low') || str_contains($nameLower, 'uluf')) {
                return rand(150000000, 400000000);
            }
            if (str_contains($nameLower, 'refrigerator') || str_contains($nameLower, 'blood bank')) {
                return rand(80000000, 200000000);
            }
            if (str_contains($nameLower, 'centrifuge')) {
                return rand(50000000, 250000000);
            }
            if (str_contains($nameLower, 'laminar') || str_contains($nameLower, 'cabinet') || str_contains($nameLower, 'downflow')) {
                return rand(40000000, 150000000);
            }
            if (str_contains($nameLower, 'analyzer') || str_contains($nameLower, 'infinite') || str_contains($nameLower, 'spark')) {
                return rand(300000000, 800000000);
            }
            if (str_contains($nameLower, 'stirrer') || str_contains($nameLower, 'vortex')) {
                return rand(15000000, 50000000);
            }
            if (str_contains($nameLower, 'clia')) {
                return rand(200000000, 500000000);
            }

            return rand(100000000, 500000000);
        }

        $nameLower = strtolower($name);

        if (str_contains($nameLower, 'kit') || str_contains($nameLower, 'qpcr') || str_contains($nameLower, 'master mix')) {
            return rand(5000000, 25000000);
        }
        if (str_contains($nameLower, 'agar') || str_contains($nameLower, 'broth')) {
            return rand(1000000, 8000000);
        }
        if (str_contains($nameLower, 'swab') || str_contains($nameLower, 'sampling')) {
            return rand(500000, 5000000);
        }
        if (str_contains($nameLower, 'tip') || str_contains($nameLower, 'pipette') || str_contains($nameLower, 'tube')) {
            return rand(500000, 3000000);
        }
        if (str_contains($nameLower, 'cuvette')) {
            return rand(2000000, 10000000);
        }
        if (str_contains($nameLower, 'petridish')) {
            return rand(1000000, 5000000);
        }
        if (str_contains($nameLower, 'igg') || str_contains($nameLower, 'tsh') || str_contains($nameLower, 'd-dimer')) {
            return rand(3000000, 15000000);
        }

        return rand(2000000, 15000000);
    }

    public function run(): void
    {
        $this->loadPrincipalProducts();

        $users = User::all();
        $departments = Department::all();
        $positions = Position::all();

        $principals = collect();
        foreach ($this->principalProducts as $name => $data) {
            $principals->push(Principal::create([
                'name' => $name,
                'code' => $data['code'],
                'description' => $name.' Medical Equipment and Supplies',
                'contact_person' => fake('id_ID')->name(),
                'phone' => fake('id_ID')->phoneNumber(),
                'email' => strtolower($data['code']).'@'.strtolower(str_replace(' ', '', $name)).'.com',
                'address' => fake('id_ID')->address(),
                'is_active' => true,
            ]));
        }

        $items = collect();
        foreach ($this->principalProducts as $principalName => $data) {
            $principal = $principals->where('code', $data['code'])->first();
            if (! $principal) {
                continue;
            }

            foreach ($data['products'] as $productName) {
                $type = $this->inferProductType($productName);
                $price = $this->generatePrice($type, $productName);

                $items->push(Item::create([
                    'name' => $productName,
                    'principal_id' => $principal->id,
                    'internal_code' => $data['code'].'-'.fake()->unique()->numerify('####'),
                    'principle_code' => $data['code'].'-'.strtoupper(Str::random(5)),
                    'unit_price' => $price,
                    'unit' => $type === 'Capital' ? 'Unit' : 'Pack',
                    'description' => $productName.' medical '.strtolower($type),
                    'is_active' => true,
                ]));
            }
        }

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
