<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BackupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'filename' => 'backup_'.now()->format('Y-m-d_H-i-s').'_'.fake()->unique()->numberBetween(1000, 9999).'.sql',
            'size' => 1024,
            'created_by' => null,
        ];
    }
}
