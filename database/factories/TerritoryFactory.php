<?php

namespace Database\Factories;

use App\Models\Territory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Territory>
 */
class TerritoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake('id_ID')->city(),
            'wilayah_code' => fake()->unique()->numerify('##.##'),
            'type' => fake()->randomElement(['region', 'province', 'city']),
            'level' => fake()->numberBetween(1, 3),
            'manager_id' => User::factory(),
        ];
    }
}
