<?php

namespace Database\Factories;

use App\Models\SalesType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesType>
 */
class SalesTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Regular Sales', 'Government Tender', 'Private Tender', 'Institutional']),
            'code' => fake()->unique()->lexify('TYPE-???'),
            'description' => fake()->sentence(),
        ];
    }
}
