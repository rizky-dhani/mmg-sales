<?php

namespace Database\Seeders;

use App\Models\Distributor;
use Illuminate\Database\Seeder;

class DistributorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $distributors = [
            [
                'name' => 'MMG',
                'code' => 'MMG',
            ],
            [
                'name' => 'MJG',
                'code' => 'MJG',
            ],
        ];

        foreach ($distributors as $distributor) {
            Distributor::updateOrCreate(
                ['code' => $distributor['code']],
                ['name' => $distributor['name']]
            );
        }
    }
}