<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'sku' => fake()->unique()->bothify('SKU-####-????'),
            'category' => fake()->randomElement(['pharmaceutical', 'medical_device', 'diagnostic', 'disposable']),
            'description' => fake()->paragraph(),
            'unit_price' => fake()->numberBetween(5000, 5000000),
            'unit_of_measure' => fake()->randomElement(['Box', 'Pcs', 'Vial', 'Bottle']),
            'stock_quantity' => fake()->numberBetween(0, 1000),
            'minimum_stock' => fake()->numberBetween(10, 50),
            'reorder_quantity' => fake()->numberBetween(50, 200),
            'is_active' => true,
            'requires_prescription' => fake()->boolean(40),
            'manufacturer' => fake()->company(),
            'expiry_date' => fake()->dateTimeBetween('+6 months', '+2 years')->format('Y-m-d'),
            'storage_requirements' => fake()->randomElement(['Room Temperature', 'Refrigerated 2-8°C', 'Cool Place']),
        ];
    }
}
