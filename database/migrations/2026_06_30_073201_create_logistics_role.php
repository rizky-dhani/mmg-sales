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

        $orderPermissions = [
            'view_any_order',
            'view_order',
            'update_order',
        ];

        foreach ($orderPermissions as $permission) {
            Permission::findOrCreate($permission);
        }

        Role::findOrCreate('Staff - Logistics')
            ->givePermissionTo($orderPermissions);
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::where('name', 'Staff - Logistics')->first();
        if ($role) {
            $role->delete();
        }
    }
};
