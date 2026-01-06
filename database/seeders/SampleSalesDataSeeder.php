<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleSalesDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure prerequisite data
        $dept = Department::where('code', 'SALES')->first() ?? Department::first();
        $headPos = Position::where('code', 'HEAD')->first() ?? Position::first();
        $rsmPos = Position::where('code', 'RSM')->first() ?? Position::first();
        $asmPos = Position::where('code', 'ASM')->first() ?? Position::first();
        $spvPos = Position::where('code', 'SPV')->first() ?? Position::first();
        $srPos = Position::where('code', 'SR')->first() ?? Position::first();
        $user = User::first();

        $city = Territory::where('type', 'city')->first();
        if (! $city) {
            $city = Territory::create(['name' => 'Sample City', 'type' => 'city', 'level' => 3]);
        }

        // 2. Configuration Data
        $customerGroup = CustomerGroup::updateOrCreate(['code' => 'GOV'], ['name' => 'Government', 'is_active' => true]);
        $segment = Segment::updateOrCreate(['code' => 'PHARMA'], ['name' => 'Pharmaceuticals']);
        $subSegment = SubSegment::updateOrCreate(['code' => 'GEN'], ['name' => 'Generic', 'segment_id' => $segment->id]);
        $salesType = SalesType::updateOrCreate(['code' => 'REG'], ['name' => 'Regular']);

        $distributor = Distributor::updateOrCreate(
            ['code' => 'DIST01'],
            ['name' => 'Main Distributor', 'city' => 'Jakarta', 'is_active' => true]
        );

        $principal = Principal::updateOrCreate(
            ['code' => 'PRIN01'],
            ['name' => 'Global Pharma Co', 'is_active' => true]
        );

        // 3. Items
        $item = Item::updateOrCreate(
            ['internal_code' => 'ITEM001'],
            [
                'name' => 'Amoxicillin 500mg',
                'principal_id' => $principal->id,
                'unit_price' => 50000,
                'unit' => 'Box',
                'is_active' => true,
            ]
        );

        // 4. Customers
        $customer = Customer::updateOrCreate(
            ['email' => 'rs_central@example.com'],
            [
                'facility_name' => 'RS Central Jakarta',
                'facility_type' => 'hospital',
                'classification' => 'tier_1',
                'address' => 'Jl. Sudirman No. 1',
                'city' => 'Jakarta',
                'is_active' => true,
                'assigned_to' => $user?->id,
            ]
        );

        // 5. Leads
        Lead::create([
            'company_name' => 'Klinik Sehat',
            'contact_person' => 'Dr. Budi',
            'email' => 'budi@kliniksehat.id',
            'phone' => '08123456789',
            'status' => 'new',
            'source' => 'referral',
            'priority' => 'high',
            'estimated_value' => 10000000,
            'customer_id' => $customer->id,
            'assigned_to' => $user?->id,
        ]);

        // 6. Orders
        for ($i = 1; $i <= 5; $i++) {
            Order::create([
                'tahun' => 2025,
                'bulan' => 1,
                'department_id' => $dept->id,
                'head_position_id' => $headPos->id,
                'rsm_asm_position_id' => $rsmPos->id,
                'spv_position_id' => $spvPos->id,
                'sr_position_id' => $srPos->id,
                'area_city_id' => $city->id,
                'end_customer_id' => $customer->id,
                'customer_group_id' => $customerGroup->id,
                'cd_ncd_type' => 'CD',
                'segment_id' => $segment->id,
                'principal_id' => $principal->id,
                'reg_inst' => 'REG',
                'sales_type_id' => $salesType->id,
                'item_id' => $item->id,
                'qty_hna' => 10 * $i,
                'total_hna_gross_sales' => 500000 * $i,
                'discount_on' => 10.00,
                'net_sales_total' => 450000 * $i,
                'sub_segment_id' => $subSegment->id,
                'jual_kso' => 'Jual',
                'distributor_id' => $distributor->id,
                'order_number' => 'MMG-ORD-2025-'.Str::padLeft($i, 8, '0'),
                'status' => 'confirmed',
                'subtotal' => 500000 * $i,
                'total_amount' => 450000 * $i,
                'order_date' => now()->subDays($i),
                'created_by' => $user?->id,
            ]);
        }
    }
}
