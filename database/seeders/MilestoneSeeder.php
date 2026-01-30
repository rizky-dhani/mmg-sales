<?php

namespace Database\Seeders;

use App\Models\Milestone;
use Illuminate\Database\Seeder;

class MilestoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $milestones = [
            [
                'name' => 'Budget Confirmed',
                'weight' => 20,
                'description' => 'Customer has allocated funds for the purchase.',
            ],
            [
                'name' => 'Decision Maker Met',
                'weight' => 15,
                'description' => 'Direct contact with the person who signs the PO.',
            ],
            [
                'name' => 'Need Validated',
                'weight' => 20,
                'description' => 'Clinical/Technical problem identified and solution agreed.',
            ],
            [
                'name' => 'Timeline Established',
                'weight' => 15,
                'description' => 'Targeted procurement quarter/month is fixed.',
            ],
            [
                'name' => 'Technical Compliance',
                'weight' => 20,
                'description' => 'Product specs meet all local/tender requirements.',
            ],
            [
                'name' => 'Trial/Demo Success',
                'weight' => 10,
                'description' => 'Physical demonstration completed and approved.',
            ],
        ];

        foreach ($milestones as $milestone) {
            Milestone::updateOrCreate(['name' => $milestone['name']], $milestone);
        }
    }
}
