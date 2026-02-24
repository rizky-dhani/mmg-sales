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
    public function run(): void
    {
        $this->call([
            PrincipalSeeder::class,
            PrincipalProductSeeder::class,
            ProductSeeder::class,
        ]);

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

        $principalIds = Principal::pluck('id')->toArray();
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
