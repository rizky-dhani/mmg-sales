<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $item = Item::factory()->create();
        $quantity = fake()->numberBetween(1, 100);
        $unitPrice = $item->unit_price;
        $subtotal = $quantity * $unitPrice;

        return [
            'order_id' => Order::factory(),
            'item_id' => $item->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'current_price' => $unitPrice,
            'price_type' => fake()->randomElement(['unit_price', 'ecatalog_price']),
            'discount_amount' => 0,
            'subtotal' => $subtotal,
            'notes' => null,
        ];
    }
}
