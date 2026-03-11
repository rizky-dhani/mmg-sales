<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake('id_ID')->company().' '.fake()->randomElement(['Hospital', 'Clinic', 'Pharmacy', 'Lab']),
            'type' => fake()->randomElement(['hospital', 'clinic', 'pharmacy', 'laboratory', 'distributor', 'other']),
            'tax_number' => fake()->unique()->numerify('##.###.###.#-###.###'),
            'address' => fake('id_ID')->address(),
            'city' => fake('id_ID')->city(),
            'state' => fake('id_ID')->state(),
            'postal_code' => fake('id_ID')->postcode(),
            'country' => 'Indonesia',
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake('id_ID')->phoneNumber(),
            'website' => fake()->url(),
            'is_active' => true,
        ];
    }
}
