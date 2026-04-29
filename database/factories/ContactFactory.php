<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
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
