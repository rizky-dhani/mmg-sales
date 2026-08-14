<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_monthly_revenue_widget',
            'view_principal_revenue_widget',
            'view_top_selling_products_widget',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Super Admin gets all
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        // Management roles
        $managementRoles = [
            'Management Director',
            'Sales Manager',
            'Marketing Manager',
            'Sales Regional Manager',
            'Sales Area Manager',
        ];

        foreach ($managementRoles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($permissions);
            }
        }
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_monthly_revenue_widget',
            'view_principal_revenue_widget',
            'view_top_selling_products_widget',
        ];

        foreach ($permissions as $permission) {
            Permission::where('name', $permission)->delete();
        }
    }
};
