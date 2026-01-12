<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $models = [
            'user',
            'department',
            'position',
            'territory',
            'company',
            'company_group',
            'contact',
            'lead',
            'product',
            'order',
            'activity',
            'visit',
            'segment',
            'sub_segment',
        ];

        $actions = ['view', 'view_any', 'create', 'update', 'delete', 'restore', 'force_delete'];

        foreach ($models as $model) {
            foreach ($actions as $action) {
                Permission::findOrCreate("{$action}_{$model}");
            }
        }

        // Create roles and assign permissions

        // Super Admin
        $superAdmin = Role::findOrCreate('Super Admin');
        // Super Admin gets all permissions via a gate in AuthServiceProvider (or similar)
        // but for Filament it's often better to assign them explicitly or use a policy.

        $bod = Role::findOrCreate('Board of Director');
        $viewPermissions = Permission::where('name', 'like', 'view_%')->get();
        $bod->givePermissionTo($viewPermissions);

        $head = Role::findOrCreate('Head');
        $head->givePermissionTo(Permission::all());

        $pm = Role::findOrCreate('ProductManager');
        $jpm = Role::findOrCreate('JrProductManager');
        $pe = Role::findOrCreate('ProductExecutive');

        $marketingRoles = [$pm, $jpm, $pe];
        foreach ($marketingRoles as $role) {
            $role->givePermissionTo([
                'view_any_product', 'view_product', 'create_product', 'update_product',
                'view_any_order', 'view_order',
                'view_any_company', 'view_company',
            ]);
        }

        $rsm = Role::findOrCreate('RegionalSalesManager');
        $asm = Role::findOrCreate('AreaSalesManager');
        $spv = Role::findOrCreate('Supervisor');
        $sr = Role::findOrCreate('SalesRepresentative');

        $salesRoles = [$rsm, $asm, $spv, $sr];
        foreach ($salesRoles as $role) {
            $role->givePermissionTo([
                'view_any_company', 'view_company', 'create_company', 'update_company',
                'view_any_contact', 'view_contact', 'create_contact', 'update_contact',
                'view_any_lead', 'view_lead', 'create_lead', 'update_lead',
                'view_any_order', 'view_order', 'create_order', 'update_order',
                'view_any_product', 'view_product',
            ]);
        }
    }
}
