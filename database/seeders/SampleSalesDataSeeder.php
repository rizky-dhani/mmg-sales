<?php

namespace Database\Seeders;

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
use Illuminate\Support\Str;

class SampleSalesDataSeeder extends Seeder
{
    protected array $principalProducts = [];

    protected function loadPrincipalProducts(): void
    {
        $filePath = database_path('../principals_products.txt');
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $usedCodes = [];
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
                    'code' => $this->generateCode($line, $usedCodes),
                    'products' => [],
                ];
            }
        }
    }

    protected function generateCode(string $name, array &$usedCodes): string
    {
        $words = preg_split('/\s+/', $name);
        if (count($words) >= 2) {
            $code = strtoupper(substr($words[0], 0, 2).substr($words[1], 0, 1));
        } else {
            $code = strtoupper(substr($name, 0, 3));
        }

        if (isset($usedCodes[$code])) {
            $counter = 1;
            while (isset($usedCodes[$code.$counter])) {
                $counter++;
            }
            $code = $code.$counter;
        }

        $usedCodes[$code] = true;

        return $code;
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

        $dept = Department::where('code', 'SALES')->first() ?? Department::first();
        $headPos = Position::where('code', 'HEAD')->first() ?? Position::first();
        $rsmPos = Position::where('code', 'RSM')->first() ?? Position::first();
        $asmPos = Position::where('code', 'ASM')->first() ?? Position::first();
        $spvPos = Position::where('code', 'SPV')->first() ?? Position::first();
        $srPos = Position::where('code', 'SR')->first() ?? Position::first();
        $user = User::first();

        $cities = Territory::where('type', 'city')->limit(5)->get();
        if ($cities->isEmpty()) {
            $cities = collect([Territory::create(['name' => 'Sample City', 'type' => 'city', 'level' => 3])]);
        }

        $govGroup = CustomerGroup::updateOrCreate(['code' => 'GOV'], ['name' => 'Government', 'is_active' => true]);
        $privGroup = CustomerGroup::updateOrCreate(['code' => 'PRIV'], ['name' => 'Private', 'is_active' => true]);

        $pharmaSegment = Segment::updateOrCreate(['code' => 'PHARMA'], ['name' => 'Pharmaceuticals']);
        $medEquipSegment = Segment::updateOrCreate(['code' => 'MEDEQ'], ['name' => 'Medical Equipment']);

        $genSubSegment = SubSegment::updateOrCreate(['code' => 'GEN'], ['name' => 'Generic', 'segment_id' => $pharmaSegment->id]);
        $dispSubSegment = SubSegment::updateOrCreate(['code' => 'DISP'], ['name' => 'Disposable', 'segment_id' => $medEquipSegment->id]);

        $regSalesType = SalesType::updateOrCreate(['code' => 'REG'], ['name' => 'Regular']);
        $tenderSalesType = SalesType::updateOrCreate(['code' => 'TENDER'], ['name' => 'Tender']);

        $distributor = Distributor::updateOrCreate(
            ['code' => 'DIST01'],
            ['name' => 'Main Distributor Jakarta', 'city' => 'Jakarta', 'is_active' => true]
        );

        foreach ($this->principalProducts as $name => $data) {
            Principal::updateOrCreate(
                ['code' => $data['code']],
                ['name' => $name, 'is_active' => true]
            );
        }
        $principalIds = Principal::pluck('id')->toArray();

        foreach ($this->principalProducts as $principalName => $data) {
            $principal = Principal::where('code', $data['code'])->first();
            if (! $principal) {
                continue;
            }

            foreach ($data['products'] as $productName) {
                $type = $this->inferProductType($productName);
                $price = $this->generatePrice($type, $productName);

                Item::updateOrCreate(
                    ['internal_code' => $data['code'].'-'.preg_replace('/[^A-Za-z0-9]/', '', strtoupper(substr($productName, 0, 8)))],
                    [
                        'name' => $productName,
                        'principal_id' => $principal->id,
                        'unit_price' => $price,
                        'unit' => $type === 'Capital' ? 'Unit' : 'Pack',
                        'is_active' => true,
                    ]
                );
            }
        }
        $itemIds = Item::pluck('id')->toArray();

        $customersData = [
            ['email' => 'rs_central@example.com', 'name' => 'RS Central Jakarta', 'type' => 'hospital', 'class' => 'tier_1'],
            ['email' => 'klinik_bunda@example.com', 'name' => 'Klinik Bunda', 'type' => 'clinic', 'class' => 'tier_2'],
            ['email' => 'apotek_jaya@example.com', 'name' => 'Apotek Jaya', 'type' => 'pharmacy', 'class' => 'tier_3'],
            ['email' => 'lab_pintar@example.com', 'name' => 'Lab Pintar', 'type' => 'laboratory', 'class' => 'tier_2'],
        ];

        foreach ($customersData as $c) {
            Customer::updateOrCreate(
                ['email' => $c['email']],
                [
                    'name' => $c['name'],
                    'type' => $c['type'],
                    'classification' => $c['class'],
                    'address' => 'Jl. Sample No. '.rand(1, 100),
                    'city' => 'Jakarta',
                    'is_active' => true,
                    'assigned_to' => $user?->id,
                ]
            );
        }
        $customerIds = Customer::pluck('id')->toArray();

        $projectStatuses = ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost'];
        $projectSources = ['website', 'referral', 'cold_call', 'trade_show', 'partner', 'other'];
        $projectPriorities = ['low', 'medium', 'high', 'urgent'];

        for ($i = 1; $i <= 15; $i++) {
            Project::create([
                'customer_name' => 'Prospect '.Str::random(5),
                'contact_person' => 'Contact '.$i,
                'email' => 'contact'.$i.'@prospect.com',
                'phone' => '0812'.rand(10000000, 99999999),
                'status' => $projectStatuses[array_rand($projectStatuses)],
                'source' => $projectSources[array_rand($projectSources)],
                'priority' => $projectPriorities[array_rand($projectPriorities)],
                'estimated_value' => rand(5000000, 100000000),
                'customer_id' => $i % 2 == 0 ? $customerIds[array_rand($customerIds)] : null,
                'assigned_to' => $user?->id,
            ]);
        }

        $orderStatuses = ['draft', 'pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'];
        $paymentStatuses = ['pending', 'partial', 'paid', 'overdue'];

        for ($i = 1; $i <= 20; $i++) {
            $selectedItem = Item::find($itemIds[array_rand($itemIds)]);
            $qty = rand(5, 100);
            $totalGross = $selectedItem->unit_price * $qty;
            $discount = rand(0, 15);
            $netTotal = $totalGross * (1 - ($discount / 100));

            Order::create([
                'tahun' => 2025,
                'bulan' => rand(1, 3),
                'department_id' => $dept->id,
                'head_position_id' => $headPos->id,
                'rsm_asm_position_id' => $rsmPos->id,
                'spv_position_id' => $spvPos->id,
                'sr_position_id' => $srPos->id,
                'area_city_id' => $cities->random()->id,
                'end_customer_id' => $customerIds[array_rand($customerIds)],
                'customer_group_id' => rand(0, 1) ? $govGroup->id : $privGroup->id,
                'cd_ncd_type' => rand(0, 1) ? 'CD' : 'NCD',
                'segment_id' => rand(0, 1) ? $pharmaSegment->id : $medEquipSegment->id,
                'principal_id' => $selectedItem->principal_id,
                'reg_inst' => rand(0, 1) ? 'REG' : 'INST',
                'sales_type_id' => rand(0, 1) ? $regSalesType->id : $tenderSalesType->id,
                'item_id' => $selectedItem->id,
                'qty_hna' => $qty,
                'total_hna_gross_sales' => $totalGross,
                'discount_on' => $discount,
                'net_sales_total' => $netTotal,
                'sub_segment_id' => rand(0, 1) ? $genSubSegment->id : $dispSubSegment->id,
                'jual_kso' => rand(0, 1) ? 'Jual' : 'KSO',
                'distributor_id' => $distributor->id,
                'order_number' => 'MMG-ORD-2025-'.Str::padLeft($i + 5, 8, '0'),
                'status' => $orderStatuses[array_rand($orderStatuses)],
                'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                'subtotal' => $totalGross,
                'total_amount' => $netTotal,
                'order_date' => now()->subDays(rand(1, 60)),
                'created_by' => $user?->id,
            ]);
        }
    }
}
