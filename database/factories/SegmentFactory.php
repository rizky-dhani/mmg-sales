<?php

namespace Database\Factories;

use App\Models\Segment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Segment>
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
