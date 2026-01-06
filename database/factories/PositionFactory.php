<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Position>
 */
class PositionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->jobTitle(),
            'code' => fake()->unique()->lexify('POS-????'),
            'level' => fake()->numberBetween(1, 5),
            'department_id' => Department::factory(),
            'description' => fake()->sentence(),
        ];
    }
}
