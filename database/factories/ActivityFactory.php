<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = [
            'call' => ['Follow up call', 'Introduction call', 'Closing negotiation'],
            'email' => ['Product catalog sent', 'Proposal draft', 'Inquiry response'],
            'meeting' => ['F2F Presentation', 'Hospital visit', 'Dinner meeting'],
            'demo' => ['Equipment trial', 'Software walkthrough'],
        ];

        $type = fake()->randomElement(array_keys($types));
        $subject = fake()->randomElement($types[$type]);

        return [
            'lead_id' => Lead::factory(),
            'user_id' => User::factory(),
            'type' => $type,
            'subject' => $subject,
            'description' => fake('id_ID')->paragraph(),
            'performed_at' => fake()->dateTimeBetween('-2 months', 'now'),
            'duration_minutes' => fake()->randomElement([15, 30, 45, 60, 90]),
            'outcome' => fake()->randomElement(['Interested', 'No Answer', 'Postponed', 'Need more info', 'Not Interested']),
        ];
    }
}
