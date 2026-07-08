<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Generate all model permissions ───────────────────────────────
        $models = [
            'customer_group', 'sub_segment', 'sales_type', 'customer', 'department',
            'distributor', 'activity', 'activity_comment', 'contact', 'product', 'lead', 'segment',
            'territory', 'position', 'principal', 'milestone', 'item', 'order',
            'target', 'user',
        ];

        $actions = ['view', 'view_any', 'create', 'update', 'delete', 'restore', 'force_delete'];

        foreach ($models as $model) {
            foreach ($actions as $action) {
                Permission::findOrCreate("{$action}_{$model}");
            }
        }

        // ── Custom order permissions ─────────────────────────────────────
        Permission::findOrCreate('update_delivery_order');
        Permission::findOrCreate('update_payment_order');

        // ── Report permissions ───────────────────────────────────────────
        $reportPermissions = [
            'view_sales_reports',
            'view_customer_reports',
            'view_product_reports',
            'view_project_reports',
            'view_lead_reports',
        ];

        foreach ($reportPermissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // ── Reference data view permissions (shared by many roles) ───────
        $viewReference = [
            'view_any_customer', 'view_customer',
            'view_any_customer_group', 'view_customer_group',
            'view_any_contact', 'view_contact',
            'view_any_segment', 'view_segment',
            'view_any_sub_segment', 'view_sub_segment',
            'view_any_territory', 'view_territory',
            'view_any_distributor', 'view_distributor',
            'view_any_principal', 'view_principal',
            'view_any_product', 'view_product',
            'view_any_sales_type', 'view_sales_type',
            'view_any_item', 'view_item',
            'view_any_department', 'view_department',
            'view_any_position', 'view_position',
        ];

        // ── 1. Super Admin ──────────────────────────────────────────────
        // Bypasses all permissions via Gate::before in AppServiceProvider
        Role::findOrCreate('Super Admin');

        // ── 2. Admin - Sales ─────────────────────────────────────────────
        // CRUD: Customer, Customer Group, Contact, Segment, Sub Segment
        $adminSalesPermissions = array_merge([
            'view_any_customer', 'view_customer', 'create_customer', 'update_customer', 'delete_customer', 'restore_customer', 'force_delete_customer',
            'view_any_customer_group', 'view_customer_group', 'create_customer_group', 'update_customer_group', 'delete_customer_group', 'restore_customer_group', 'force_delete_customer_group',
            'view_any_contact', 'view_contact', 'create_contact', 'update_contact', 'delete_contact', 'restore_contact', 'force_delete_contact',
            'view_any_segment', 'view_segment', 'create_segment', 'update_segment', 'delete_segment', 'restore_segment', 'force_delete_segment',
            'view_any_sub_segment', 'view_sub_segment', 'create_sub_segment', 'update_sub_segment', 'delete_sub_segment', 'restore_sub_segment', 'force_delete_sub_segment',
        ], $viewReference);

        Role::findOrCreate('Admin - Sales')->syncPermissions($adminSalesPermissions);

        // ── 3. Supervisor - Import & Purchasing ──────────────────────────
        // CRUD: Principal, Product, Distributor
        $supervisorIpPermissions = array_merge([
            'view_any_principal', 'view_principal', 'create_principal', 'update_principal', 'delete_principal', 'restore_principal', 'force_delete_principal',
            'view_any_product', 'view_product', 'create_product', 'update_product', 'delete_product', 'restore_product', 'force_delete_product',
            'view_any_distributor', 'view_distributor', 'create_distributor', 'update_distributor', 'delete_distributor', 'restore_distributor', 'force_delete_distributor',
        ], $viewReference);

        Role::findOrCreate('Supervisor - Import & Purchasing')->syncPermissions($supervisorIpPermissions);

        // ── 4. Sales & Marketing department roles ────────────────────────
        // CRUD: Lead, Activity, Order, Target
        $smPermissions = array_merge([
            // Lead
            'view_any_lead', 'view_lead', 'create_lead', 'update_lead', 'delete_lead',
            // Activity
            'view_any_activity', 'view_activity', 'create_activity', 'update_activity', 'delete_activity',
            // Activity Comment
            'view_any_activity_comment', 'view_activity_comment', 'create_activity_comment', 'update_activity_comment', 'delete_activity_comment',
            // Order
            'view_any_order', 'view_order', 'create_order', 'update_order', 'delete_order',
            // Target
            'view_any_target', 'view_target', 'create_target', 'update_target', 'delete_target',
        ], $viewReference);

        $smRoles = [
            'Staff - Sales',
            'Supervisor - Sales',
            'Area Sales Manager - Sales',
            'Regional Sales Manager - Sales',
            'Staff - Marketing',
            'Manager - Marketing',
            'Manager - Sales',
        ];

        foreach ($smRoles as $roleName) {
            Role::findOrCreate($roleName)->syncPermissions($smPermissions);
        }

        // ── 5. Director - Management ─────────────────────────────────────
        // Reports access + view reference data + view comments
        $directorPermissions = array_merge($reportPermissions, $viewReference, [
            'view_any_activity_comment', 'view_activity_comment',
        ]);

        Role::findOrCreate('Director - Management')->syncPermissions($directorPermissions);

        // ── 6. Report access (add report permissions to specific roles) ──
        $reportRoles = [
            'Regional Sales Manager - Sales',
            'Area Sales Manager - Sales',
            'Manager - Marketing',
            'Manager - Sales',
        ];

        foreach ($reportRoles as $roleName) {
            Role::where('name', $roleName)->first()?->givePermissionTo($reportPermissions);
        }

        // ── Clean up: strip permissions from old generic roles ───────────
        $oldGenericRoles = ['Admin', 'Director', 'Staff', 'Supervisor', 'Regional Sales Manager', 'Area Sales Manager', 'Manager'];

        foreach ($oldGenericRoles as $roleName) {
            Role::where('name', $roleName)->first()?->syncPermissions([]);
        }

        // ── Clean up: strip permissions from non-S&M dept roles ─────────
        $otherDeptRoles = [
            'Staff - TSS', 'Staff - Finance & Accounting', 'Staff - RQA',
            'Supervisor - Finance & Accounting', 'Supervisor - TSS',
            'Manager - TSS',
        ];

        foreach ($otherDeptRoles as $roleName) {
            Role::where('name', $roleName)->first()?->syncPermissions([]);
        }

        // ── 7. Staff - Logistics ──────────────────────────────────────────
        // Full order visibility + shipping status updates
        Role::findOrCreate('Staff - Logistics')
            ->syncPermissions([
                'view_any_order', 'view_order', 'update_order',
            ])
            ->givePermissionTo('update_delivery_order');

        // ── 8. Staff - Finance & Accounting ──────────────────────────────
        Role::findOrCreate('Staff - Finance & Accounting')
            ->givePermissionTo('update_payment_order');
    }
}
