<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $opportunityTypes = [
            'Medical Equipment Procurement',
            'Surgical Supply Tender',
            'Diagnostic Imaging Service Contract',
            'Patient Monitoring System Upgrade',
            'Lab Equipment Maintenance',
            'Healthcare IT Implementation',
            'Pharmaceutical Distribution Partnership',
            'Emergency Room Refurbishment',
        ];

        return [
            'title' => fake()->randomElement($opportunityTypes).' - '.fake()->city(),
            'customer_name' => fake('id_ID')->company(),
            'contact_person' => fake('id_ID')->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake('id_ID')->phoneNumber(),
            'status' => fake()->randomElement(['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost']),
            'source' => fake()->randomElement(['website', 'referral', 'cold_call', 'trade_show', 'partner', 'other']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'estimated_value' => $val = fake()->numberBetween(10000000, 1000000000),
            'estimated_revenue' => $val * rand(80, 100) / 100,
            'expected_closing_date' => $closing = fake()->dateTimeBetween('now', '+6 months'),
            'estimated_completion_date' => fake()->dateTimeBetween($closing, '+8 months'),
            'financial_goal' => $val * 1.1,
            'notes' => fake()->paragraph(),
            'customer_id' => fake()->boolean(30) ? Customer::factory() : null,
            'assigned_to' => User::factory(),
            'position' => str()->random(10), // Flowforge expects a string position
        ];
    }
}
