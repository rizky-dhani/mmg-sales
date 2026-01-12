<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'first_name' => fake('id_ID')->firstName(),
            'last_name' => fake('id_ID')->lastName(),
            'position' => fake()->jobTitle(),
            'department' => fake()->randomElement(['Purchasing', 'Medical', 'Finance', 'Logistics']),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake('id_ID')->phoneNumber(),
            'mobile' => fake('id_ID')->phoneNumber(),
            'is_primary' => fake()->boolean(20),
            'is_billing_contact' => fake()->boolean(10),
            'notes' => fake()->sentence(),
        ];
    }
}
