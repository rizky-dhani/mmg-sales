<?php

namespace Database\Factories;

use App\Models\Segment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubSegment>
 */
class SubSegmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'code' => fake()->unique()->lexify('SUB-???'),
            'segment_id' => Segment::factory(),
            'description' => fake()->sentence(),
        ];
    }
}
