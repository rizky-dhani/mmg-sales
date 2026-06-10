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

        $newPermission = Permission::findOrCreate('view_project_reports');

        $oldPermission = Permission::where('name', 'view_pipeline_reports')->first();
        if ($oldPermission) {
            foreach (Role::all() as $role) {
                if ($role->hasPermissionTo($oldPermission)) {
                    $role->givePermissionTo($newPermission);
                    $role->revokePermissionTo($oldPermission);
                }
            }

            $oldPermission->delete();
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $oldPermission = Permission::findOrCreate('view_pipeline_reports');

        $newPermission = Permission::where('name', 'view_project_reports')->first();
        if ($newPermission) {
            foreach (Role::all() as $role) {
                if ($role->hasPermissionTo($newPermission)) {
                    $role->givePermissionTo($oldPermission);
                    $role->revokePermissionTo($newPermission);
                }
            }

            $newPermission->delete();
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
