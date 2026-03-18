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

        // Super Admin - bypasses all permissions via Gate::before in AppServiceProvider
        Role::findOrCreate('Super Admin');

        // Admin - CRUD permissions based on department
        // Sales dept: CRUD Customer
        // Import & Purchasing dept: CRUD Product and Principal
        $admin = Role::findOrCreate('Admin');
        $admin->givePermissionTo([
            // View permissions for reference data
            'view_any_user', 'view_user',
            'view_any_department', 'view_department',
            'view_any_position', 'view_position',
            'view_any_territory', 'view_territory',
            'view_any_customer_group', 'view_customer_group',
            'view_any_segment', 'view_segment',
            'view_any_sub_segment', 'view_sub_segment',
            'view_any_distributor', 'view_distributor',
            'view_any_sales_type', 'view_sales_type',
            'view_any_item', 'view_item',
            'view_any_contact', 'view_contact',
            'view_any_principal', 'view_principal',
            'view_any_product', 'view_product',
            // Admin can CRUD Customer (Sales dept)
            'view_any_customer', 'view_customer', 'create_customer', 'update_customer', 'delete_customer',
            // Admin can CRUD Product and Principal (Import & Purchasing dept)
            'create_product', 'update_product', 'delete_product',
            'create_principal', 'update_principal', 'delete_principal',
        ]);

        // Define Sales CRUD permissions (Project, Activity, Order)
        $salesCrudPermissions = [
            // View permissions for reference data
            'view_any_customer', 'view_customer',
            'view_any_customer_group', 'view_customer_group',
            'view_any_contact', 'view_contact',
            'view_any_territory', 'view_territory',
            'view_any_distributor', 'view_distributor',
            'view_any_principal', 'view_principal',
            'view_any_product', 'view_product',
            'view_any_sales_type', 'view_sales_type',
            'view_any_item', 'view_item',
            'view_any_segment', 'view_segment',
            'view_any_sub_segment', 'view_sub_segment',
            'view_any_department', 'view_department',
            'view_any_position', 'view_position',
            // CRUD Project, Activity, Order
            'view_any_project', 'view_project', 'create_project', 'update_project', 'delete_project',
            'view_any_activity', 'view_activity', 'create_activity', 'update_activity', 'delete_activity',
            'view_any_order', 'view_order', 'create_order', 'update_order', 'delete_order',
        ];

        // Define view-only permissions for oversight roles (Director)
        $oversightViewPermissions = [
            // View permissions for reference data
            'view_any_customer', 'view_customer',
            'view_any_customer_group', 'view_customer_group',
            'view_any_contact', 'view_contact',
            'view_any_territory', 'view_territory',
            'view_any_distributor', 'view_distributor',
            'view_any_principal', 'view_principal',
            'view_any_product', 'view_product',
            'view_any_sales_type', 'view_sales_type',
            'view_any_item', 'view_item',
            'view_any_segment', 'view_segment',
            'view_any_sub_segment', 'view_sub_segment',
            'view_any_department', 'view_department',
            'view_any_position', 'view_position',
            // View-only Project, Activity, Order
            'view_any_project', 'view_project',
            'view_any_activity', 'view_activity',
            'view_any_order', 'view_order',
        ];

        // Director - View-only oversight of Project, Activity, Order from Manager and below
        $director = Role::findOrCreate('Director');
        $director->givePermissionTo($oversightViewPermissions);

        // Staff (Sales dept) - CRUD Project, Activity, Order
        $staff = Role::findOrCreate('Staff');
        $staff->givePermissionTo($salesCrudPermissions);

        // Supervisor - Same permissions as Staff
        $supervisor = Role::findOrCreate('Supervisor');
        $supervisor->givePermissionTo($salesCrudPermissions);

        // Regional Sales Manager - Same permissions as Supervisor
        $rsm = Role::findOrCreate('Regional Sales Manager');
        $rsm->givePermissionTo($salesCrudPermissions);

        // Area Sales Manager - Same permissions as RSM
        $asm = Role::findOrCreate('Area Sales Manager');
        $asm->givePermissionTo($salesCrudPermissions);

        // Manager - Same permissions as RSM/ASM
        $manager = Role::findOrCreate('Manager');
        $manager->givePermissionTo($salesCrudPermissions);
    }
}
