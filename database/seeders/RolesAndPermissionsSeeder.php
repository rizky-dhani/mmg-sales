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
            'customer',
            'contact',
            'lead',
            'product',
            'order',
        ];

        $actions = ['view', 'view_any', 'create', 'update', 'delete', 'restore', 'force_delete'];

        foreach ($models as $model) {
            foreach ($actions as $action) {
                Permission::findOrCreate("{$action}_{$model}");
            }
        }

        // Create roles and assign permissions

        // Super Admin
        $superAdmin = Role::findOrCreate('SuperAdmin');
        // SuperAdmin gets all permissions via a gate in AuthServiceProvider (or similar)
        // but for Filament it's often better to assign them explicitly or use a policy.

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
                'view_any_customer', 'view_customer',
            ]);
        }

        $rsm = Role::findOrCreate('RegionalSalesManager');
        $asm = Role::findOrCreate('AreaSalesManager');
        $spv = Role::findOrCreate('Supervisor');
        $sr = Role::findOrCreate('SalesRepresentative');

        $salesRoles = [$rsm, $asm, $spv, $sr];
        foreach ($salesRoles as $role) {
            $role->givePermissionTo([
                'view_any_customer', 'view_customer', 'create_customer', 'update_customer',
                'view_any_contact', 'view_contact', 'create_contact', 'update_contact',
                'view_any_lead', 'view_lead', 'create_lead', 'update_lead',
                'view_any_order', 'view_order', 'create_order', 'update_order',
                'view_any_product', 'view_product',
            ]);
        }
    }
}
