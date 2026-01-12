<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyGroup>
 */
class CompanyGroupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true).' Group',
            'code' => fake()->unique()->lexify('GRP-???'),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
