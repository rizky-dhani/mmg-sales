<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'facility_name' => fake('id_ID')->company().' '.fake()->randomElement(['Hospital', 'Clinic', 'Pharmacy', 'Lab']),
            'facility_type' => fake()->randomElement(['hospital', 'clinic', 'pharmacy', 'laboratory', 'distributor', 'other']),
            'classification' => fake()->randomElement(['tier_1', 'tier_2', 'tier_3']),
            'tax_number' => fake()->unique()->numerify('##.###.###.#-###.###'),
            'address' => fake('id_ID')->address(),
            'city' => fake('id_ID')->city(),
            'state' => fake('id_ID')->state(),
            'postal_code' => fake('id_ID')->postcode(),
            'country' => 'Indonesia',
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake('id_ID')->phoneNumber(),
            'website' => fake()->url(),
            'credit_limit' => fake()->numberBetween(50000000, 500000000),
            'payment_terms_days' => fake()->randomElement([30, 60, 90]),
            'is_active' => true,
            'assigned_to' => User::factory(),
        ];
    }
}
