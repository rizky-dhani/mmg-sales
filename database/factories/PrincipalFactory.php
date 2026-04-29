<?php

namespace Database\Factories;

use App\Models\Principal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Principal>
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
