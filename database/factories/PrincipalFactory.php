<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Principal>
 */
class PrincipalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company().' '.fake()->randomElement(['Ltd', 'Inc', 'GmbH', 'S.A.']),
            'initial' => str(fake()->unique()->lexify('????'))->upper()->limit(10, ''),
            'description' => fake()->paragraph(),
            'is_active' => true,
        ];
    }
}
