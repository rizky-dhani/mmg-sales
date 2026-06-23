<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\Distributor;
use App\Models\Item;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Position;
use App\Models\Principal;
use App\Models\Segment;
use App\Models\SubSegment;
use App\Models\Territory;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class OrdersImport implements ToCollection
{
    /**
     * @throws Exception
     */
    public function collection(Collection $collection)
    {
        // Skip header rows (index 0, 1, 2)
        $dataRows = $collection->slice(3);

        DB::transaction(function () use ($dataRows) {
            foreach ($dataRows as $row) {
                if (empty($row[1])) {
                    continue;
                }

                // Deterministic Order Number for Duplicate Detection
                // MMG-ORD-YYYY-MM-HASH(SR, Customer, Item, Qty, Total)
                $hashInput = implode('|', [
                    $row[8],  // SR
                    $row[11], // End Customer
                    $row[20], // Item Name
                    $row[21], // Qty
                    $row[23], // Total HNA
                ]);
                $orderNumber = 'MMG-ORD-'.$row[1].'-'.$row[2].'-'.strtoupper(substr(sha1($hashInput), 0, 8));

                // Check for duplicates
                if (Order::where('order_number', $orderNumber)->exists()) {
                    continue; // Skip this row
                }

                // Strict Lookups
                $department = Department::where('name', $row[3])->first() ?? throw new Exception("Department not found: {$row[3]}");
                $head = Position::where('name', $row[4])->first() ?? throw new Exception("Head Position not found: {$row[4]}");
                $pm = Position::where('name', $row[5])->first() ?? throw new Exception("PM/JPM/PE Position not found: {$row[5]}");
                $rsm = Position::where('name', $row[6])->first() ?? throw new Exception("RSM/ASM Position not found: {$row[6]}");
                $spv = Position::where('name', $row[7])->first() ?? throw new Exception("SPV Position not found: {$row[7]}");
                $sr = Position::where('name', $row[8])->first() ?? throw new Exception("SR Position not found: {$row[8]}");

                $area = Territory::where('name', $row[9])->first() ?? throw new Exception("Area/City not found: {$row[9]}");
                $endCustomer = Customer::where('name', $row[11])->first() ?? throw new Exception("End Customer not found: {$row[11]}");

                $segment = Segment::where('name', $row[14])->first() ?? throw new Exception("Segment not found: {$row[14]}");
                $principal = Principal::where('name', $row[15])->first() ?? throw new Exception("Principal not found: {$row[15]}");
                // sales_type_id is stored as string directly (e.g. INAPROC, non-INAPROC)
                $item = Item::where('name', $row[20])->first() ?? throw new Exception("Item not found: {$row[20]}");
                $distributor = Distributor::where('name', $row[28])->first() ?? throw new Exception("Distributor not found: {$row[28]}");

                // Optional Lookups
                $origCustomer = Customer::where('name', $row[10])->first();
                $custGroup = CustomerGroup::where('name', $row[12])->first();
                $subSegment = SubSegment::where('name', $row[26])->first();
                $lead = Lead::where('title', $row[16])->first();

                Order::create([
                    'tahun' => (int) $row[1],
                    'bulan' => (int) $row[2],
                    'department_id' => $department->id,
                    'head_position_id' => $head->id,
                    'pm_jpm_pe_position_id' => $pm->id,
                    'rsm_asm_position_id' => $rsm->id,
                    'spv_position_id' => $spv->id,
                    'sr_position_id' => $sr->id,
                    'area_city_id' => $area->id,
                    'end_customer_id' => $endCustomer->id,
                    'customer_group_id' => $custGroup?->id,
                    'cd_ncd_type' => $row[13],
                    'ncd_subtype' => $row[14] ?? null,
                    'segment_id' => $segment->id,
                    'principal_id' => $principal->id,
                    'reg_inst' => $row[16],
                    'sales_type_id' => $row[17],
                    'item_id' => $item->id,
                    'qty_hna' => (int) $row[21],
                    'total_hna_gross_sales' => (float) $row[23],
                    'discount_on' => (float) $row[24],
                    'net_sales_total' => (float) $row[25],
                    'sub_segment_id' => $subSegment?->id,
                    'jual_kso' => $row[27],
                    'distributor_id' => $distributor->id,
                    'order_number' => $orderNumber,
                    'original_customer_id' => $origCustomer?->id ?? $endCustomer->id,
                    'lead_id' => $lead?->id,
                    'order_date' => now(),
                    'status' => 'pending',
                ]);
            }
        });
    }
}
