<?php

namespace Database\Factories;

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
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(1000000, 100000000);
        $tax = $subtotal * 0.11;
        $discount = fake()->numberBetween(0, 5000000);
        $total = $subtotal + $tax - $discount;

        return [
            'tahun' => now()->year,
            'bulan' => now()->month,
            'department_id' => Department::factory(),
            'head_position_id' => Position::factory(),
            'pm_jpm_pe_position_id' => Position::factory(),
            'rsm_asm_position_id' => Position::factory(),
            'spv_position_id' => Position::factory(),
            'sr_position_id' => Position::factory(),
            'area_city_id' => Territory::factory(),
            'end_customer_id' => Customer::factory(),
            'customer_group_id' => CustomerGroup::factory(),
            'cd_ncd_type' => fake()->randomElement(['CD', 'NCD']),
            'ncd_subtype' => fake()->randomElement(['A', 'B', 'C', null]),
            'segment_id' => Segment::factory(),
            'principal_id' => Principal::factory(),
            'reg_inst' => fake()->randomElements(['REG', 'INST', 'Consumable'], random_int(1, 2)),
            'sales_type_id' => fake()->randomElement(['INAPROC', 'non-INAPROC']),
            'item_id' => Item::factory(),
            'qty_hna' => fake()->numberBetween(1, 100),
            'total_hna_gross_sales' => $subtotal,
            'discount_on' => fake()->randomFloat(2, 0, 20),
            'net_sales_total' => $total,
            'sub_segment_id' => SubSegment::factory(),
            'jual_kso' => fake()->randomElement(['Jual', 'KSO']),
            'distributor_id' => Distributor::factory(),
            'original_customer_id' => Customer::factory(),
            'lead_id' => Lead::factory(),
            'status' => fake()->randomElement(['draft', 'pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned']),
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'discount_amount' => $discount,
            'total_amount' => $total,
            'order_date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'payment_method' => fake()->randomElement(['Transfer', 'Credit Card', 'Cash']),
            'created_by' => User::factory(),
        ];
    }
}
