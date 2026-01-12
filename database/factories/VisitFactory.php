<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Company;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visit>
 */
class VisitFactory extends Factory
{
    protected $model = Visit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'contact_id' => Contact::factory(),
            'visit_started_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'visit_ended_at' => function (array $attributes) {
                return \Illuminate\Support\Carbon::parse($attributes['visit_started_at'])->addMinutes(rand(30, 120));
            },
            'location' => $this->faker->address(),
            'purpose' => $this->faker->sentence(),
            'expectations' => $this->faker->paragraph(),
            'targets' => $this->faker->paragraph(),
            'summary_notes' => $this->faker->paragraph(),
            'stakeholder_feedback' => $this->faker->paragraph(),
            'is_worth_keeping' => $this->faker->boolean(),
        ];
    }
}