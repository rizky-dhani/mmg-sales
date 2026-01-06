<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodDept = Department::where('code', 'PROD')->first();
        $salesDept = Department::where('code', 'SALES')->first();

        $positions = [
            // PROD Department
            ['name' => 'Head', 'code' => 'HEAD', 'department_id' => $prodDept->id, 'level' => 1],
            ['name' => 'Product Manager', 'code' => 'PM', 'department_id' => $prodDept->id, 'level' => 2],
            ['name' => 'Jr Product Manager', 'code' => 'JPM', 'department_id' => $prodDept->id, 'level' => 3],
            ['name' => 'Product Executive', 'code' => 'PE', 'department_id' => $prodDept->id, 'level' => 4],

            // SALES Department
            ['name' => 'Regional Sales Manager', 'code' => 'RSM', 'department_id' => $salesDept->id, 'level' => 1],
            ['name' => 'Area Sales Manager', 'code' => 'ASM', 'department_id' => $salesDept->id, 'level' => 2],
            ['name' => 'Supervisor', 'code' => 'SPV', 'department_id' => $salesDept->id, 'level' => 3],
            ['name' => 'Sales Representative', 'code' => 'SR', 'department_id' => $salesDept->id, 'level' => 4],
        ];

        foreach ($positions as $position) {
            Position::updateOrCreate(['code' => $position['code']], $position);
        }

        // Setup hierarchy parents
        $head = Position::where('code', 'HEAD')->first();
        $pm = Position::where('code', 'PM')->first();
        $jpm = Position::where('code', 'JPM')->first();
        $pe = Position::where('code', 'PE')->first();

        $pm->update(['parent_id' => $head->id]);
        $jpm->update(['parent_id' => $pm->id]);
        $pe->update(['parent_id' => $pm->id]);

        $rsm = Position::where('code', 'RSM')->first();
        $asm = Position::where('code', 'ASM')->first();
        $spv = Position::where('code', 'SPV')->first();
        $sr = Position::where('code', 'SR')->first();

        $rsm->update(['parent_id' => $head->id]);
        $asm->update(['parent_id' => $rsm->id]);
        $spv->update(['parent_id' => $asm->id]);
        $sr->update(['parent_id' => $spv->id]);
    }
}
