<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
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
        $password = Hash::make('Mmg2025!');

        $users = [
            [
                'name' => 'Dr. Ahmad',
                'email' => 'ahmad@mmg.id',
                'role' => 'Head',
                'position_code' => 'HEAD',
                'dept_code' => 'PROD',
                'manager_email' => null,
            ],
            [
                'name' => 'Budi',
                'email' => 'budi@mmg.id',
                'role' => 'ProductManager',
                'position_code' => 'PM',
                'dept_code' => 'PROD',
                'manager_email' => 'ahmad@mmg.id',
            ],
            [
                'name' => 'Citra',
                'email' => 'citra@mmg.id',
                'role' => 'JrProductManager',
                'position_code' => 'JPM',
                'dept_code' => 'PROD',
                'manager_email' => 'budi@mmg.id',
            ],
            [
                'name' => 'Dian',
                'email' => 'dian@mmg.id',
                'role' => 'ProductExecutive',
                'position_code' => 'PE',
                'dept_code' => 'PROD',
                'manager_email' => 'budi@mmg.id',
            ],
            [
                'name' => 'Eko',
                'email' => 'eko@mmg.id',
                'role' => 'RegionalSalesManager',
                'position_code' => 'RSM',
                'dept_code' => 'SALES',
                'manager_email' => 'ahmad@mmg.id',
            ],
            [
                'name' => 'Fajar',
                'email' => 'fajar@mmg.id',
                'role' => 'AreaSalesManager',
                'position_code' => 'ASM',
                'dept_code' => 'SALES',
                'manager_email' => 'eko@mmg.id',
            ],
            [
                'name' => 'Gilang',
                'email' => 'gilang@mmg.id',
                'role' => 'Supervisor',
                'position_code' => 'SPV',
                'dept_code' => 'SALES',
                'manager_email' => 'fajar@mmg.id',
            ],
            [
                'name' => 'Hendra',
                'email' => 'hendra@mmg.id',
                'role' => 'SalesRepresentative',
                'position_code' => 'SR',
                'dept_code' => 'SALES',
                'manager_email' => 'gilang@mmg.id',
            ],
        ];

        foreach ($users as $userData) {
            $dept = Department::where('code', $userData['dept_code'])->first();
            $pos = Position::where('code', $userData['position_code'])->first();
            $manager = $userData['manager_email'] ? User::where('email', $userData['manager_email'])->first() : null;

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $password,
                    'department_id' => $dept?->id,
                    'position_id' => $pos?->id,
                    'manager_id' => $manager?->id,
                ]
            );

            $user->syncRoles([$userData['role']]);
        }

        // Ensure there's a SuperAdmin for development
        $admin = User::updateOrCreate(
            ['email' => 'admin@mmg.id'],
            [
                'name' => 'Super Admin',
                'password' => $password,
            ]
        );
        $admin->syncRoles(['SuperAdmin']);
    }
}
