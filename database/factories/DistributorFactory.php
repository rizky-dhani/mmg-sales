<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Distributor>
 */
class DistributorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'PT '.fake('id_ID')->company(),
            'code' => fake()->unique()->lexify('DIST-????'),
            'address' => fake('id_ID')->address(),
            'city' => fake('id_ID')->city(),
            'phone' => fake('id_ID')->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
