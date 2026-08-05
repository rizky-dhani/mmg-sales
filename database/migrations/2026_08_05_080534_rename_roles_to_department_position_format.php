<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $renames = [
            'Admin - Sales'                  => 'Sales Admin',
            'Admin Finance - Finance & Accounting' => 'Finance & Accounting Admin',
            'Admin Logistics - Logistics'    => 'Logistics Admin',
            'Area Sales Manager - Sales'     => 'Sales Area Manager',
            'Director - Management'          => 'Management Director',
            'Manager - Marketing'            => 'Marketing Manager',
            'Manager - Sales'                => 'Sales Manager',
            'Regional Sales Manager - Sales' => 'Sales Regional Manager',
            'Staff - Marketing'              => 'Marketing Staff',
            'Staff - Sales'                  => 'Sales Staff',
            'Supervisor - Import & Purchasing' => 'Import & Purchasing Supervisor',
            'Supervisor - Sales'             => 'Sales Supervisor',
            'Staff - Logistics'              => 'Logistics Staff',
            'Staff - Finance & Accounting'   => 'Finance & Accounting Staff',
        ];

        foreach ($renames as $old => $new) {
            DB::table('roles')
                ->where('name', $old)
                ->update(['name' => $new]);
        }
    }

    public function down(): void
    {
        $renames = [
            'Sales Admin'                    => 'Admin - Sales',
            'Finance & Accounting Admin'     => 'Admin Finance - Finance & Accounting',
            'Logistics Admin'                => 'Admin Logistics - Logistics',
            'Sales Area Manager'             => 'Area Sales Manager - Sales',
            'Management Director'            => 'Director - Management',
            'Marketing Manager'              => 'Manager - Marketing',
            'Sales Manager'                  => 'Manager - Sales',
            'Sales Regional Manager'         => 'Regional Sales Manager - Sales',
            'Marketing Staff'                => 'Staff - Marketing',
            'Sales Staff'                    => 'Staff - Sales',
            'Import & Purchasing Supervisor' => 'Supervisor - Import & Purchasing',
            'Sales Supervisor'               => 'Supervisor - Sales',
            'Logistics Staff'                => 'Staff - Logistics',
            'Finance & Accounting Staff'     => 'Staff - Finance & Accounting',
        ];

        foreach ($renames as $old => $new) {
            DB::table('roles')
                ->where('name', $old)
                ->update(['name' => $new]);
        }
    }
};
