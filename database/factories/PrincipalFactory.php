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
            'code' => fake()->unique()->lexify('PRIN-????'),
            'description' => fake()->paragraph(),
            'contact_person' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'is_active' => true,
        ];
    }
}
