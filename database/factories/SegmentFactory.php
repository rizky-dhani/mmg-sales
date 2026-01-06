<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Segment>
 */
class SegmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Pharmaceuticals', 'Medical Devices', 'Diagnostics', 'Consumer Health']),
            'code' => fake()->unique()->lexify('SEG-???'),
            'description' => fake()->sentence(),
        ];
    }
}
