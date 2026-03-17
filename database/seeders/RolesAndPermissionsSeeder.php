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
            'customer_group',
            'contact',
            'project',
            'product',
            'order',
            'activity',
            'segment',
            'sub_segment',
            'distributor',
            'principal',
            'sales_type',
            'item',
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

        // Director - Management level with broad access but not full admin
        $director = Role::findOrCreate('Director');
        $director->givePermissionTo([
            // View permissions for all models
            'view_any_user', 'view_user',
            'view_any_department', 'view_department',
            'view_any_position', 'view_position',
            'view_any_territory', 'view_territory',
            'view_any_customer', 'view_customer',
            'view_any_customer_group', 'view_customer_group',
            'view_any_contact', 'view_contact',
            'view_any_project', 'view_project',
            'view_any_product', 'view_product',
            'view_any_order', 'view_order',
            'view_any_activity', 'view_activity',
            'view_any_segment', 'view_segment',
            'view_any_sub_segment', 'view_sub_segment',
            'view_any_distributor', 'view_distributor',
            'view_any_principal', 'view_principal',
            'view_any_sales_type', 'view_sales_type',
            'view_any_item', 'view_item',
            // Management permissions for key operational models
            'create_customer', 'update_customer', 'delete_customer',
            'create_contact', 'update_contact', 'delete_contact',
            'create_project', 'update_project', 'delete_project',
            'create_order', 'update_order', 'delete_order',
            'create_activity', 'update_activity', 'delete_activity',
        ]);

        // Staff - Operational level with day-to-day work permissions
        $staff = Role::findOrCreate('Staff');
        $staff->givePermissionTo([
            // View permissions for operational data
            'view_any_customer', 'view_customer',
            'view_any_contact', 'view_contact',
            'view_any_project', 'view_project',
            'view_any_product', 'view_product',
            'view_any_order', 'view_order',
            'view_any_activity', 'view_activity',
            'view_any_territory', 'view_territory',
            'view_any_distributor', 'view_distributor',
            'view_any_principal', 'view_principal',
            'view_any_sales_type', 'view_sales_type',
            'view_any_item', 'view_item',
            // Reference data view-only
            'view_any_department', 'view_department',
            'view_any_position', 'view_position',
            'view_any_segment', 'view_segment',
            'view_any_sub_segment', 'view_sub_segment',
            'view_any_customer_group', 'view_customer_group',
            // Operational create/update permissions
            'create_customer', 'update_customer',
            'create_contact', 'update_contact',
            'create_project', 'update_project',
            'create_order', 'update_order',
            'create_activity', 'update_activity',
        ]);

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
                'view_any_project', 'view_project', 'create_project', 'update_project',
                'view_any_order', 'view_order', 'create_order', 'update_order',
                'view_any_product', 'view_product',
                'view_any_activity', 'view_activity', 'create_activity', 'update_activity',
            ]);
        }
    }
}
