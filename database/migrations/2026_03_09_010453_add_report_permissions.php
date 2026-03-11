<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $reportPermissions = [
            'view_sales_reports',
            'view_product_reports',
            'view_customer_reports',
            'view_pipeline_reports',
        ];

        foreach ($reportPermissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($reportPermissions);
        }

        $bod = Role::where('name', 'Board of Director')->first();
        if ($bod) {
            $bod->givePermissionTo($reportPermissions);
        }

        $head = Role::where('name', 'Head')->first();
        if ($head) {
            $head->givePermissionTo($reportPermissions);
        }

        $rsm = Role::where('name', 'RegionalSalesManager')->first();
        $asm = Role::where('name', 'AreaSalesManager')->first();
        $spv = Role::where('name', 'Supervisor')->first();
        $sr = Role::where('name', 'SalesRepresentative')->first();

        $salesReportPermissions = ['view_sales_reports', 'view_customer_reports', 'view_pipeline_reports'];

        foreach ([$rsm, $asm, $spv, $sr] as $role) {
            if ($role) {
                $role->givePermissionTo($salesReportPermissions);
            }
        }

        $pm = Role::where('name', 'ProductManager')->first();
        $jpm = Role::where('name', 'JrProductManager')->first();
        $pe = Role::where('name', 'ProductExecutive')->first();

        foreach ([$pm, $jpm, $pe] as $role) {
            if ($role) {
                $role->givePermissionTo(['view_sales_reports', 'view_product_reports', 'view_customer_reports']);
            }
        }
    }

    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $reportPermissions = [
            'view_sales_reports',
            'view_product_reports',
            'view_customer_reports',
            'view_pipeline_reports',
        ];

        foreach ($reportPermissions as $permission) {
            Permission::where('name', $permission)->delete();
        }
    }
};
