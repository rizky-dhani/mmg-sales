<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there's a Super Admin for development
        $admin = User::updateOrCreate(
            ['email' => 'superadmin@medquest.co.id'],
            [
                'name' => 'Super Admin',
                'password' => 'Superadmin2025!',
            ]
        );
        $admin->assignRole(['Super Admin']);
        $password = Hash::make('Mmg2025!');
    }
}
