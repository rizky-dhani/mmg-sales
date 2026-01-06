<?php

namespace Database\Factories;

use App\Models\Principal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'internal_code' => fake()->unique()->bothify('ITEM-####-????'),
            'principle_code' => fake()->unique()->bothify('PCODE-#####'),
            'principal_id' => Principal::factory(),
            'description' => fake()->sentence(),
            'unit_price' => fake()->numberBetween(10000, 10000000),
            'unit' => fake()->randomElement(['Box', 'Pcs', 'Vial', 'Ampoule', 'Bottle']),
            'is_active' => true,
        ];
    }
}
