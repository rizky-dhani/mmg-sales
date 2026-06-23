<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rolesToScope = ['Admin', 'Director', 'Manager', 'Regional Sales Manager', 'Area Sales Manager', 'Supervisor', 'Staff'];

        $assignments = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->select('users.id as user_id', 'roles.name as role_name', 'users.department_id', 'departments.name as dept_name')
            ->whereNotNull('users.department_id')
            ->whereIn('roles.name', $rolesToScope)
            ->get();

        foreach ($assignments as $assignment) {
            $newRoleName = "{$assignment->role_name} - {$assignment->dept_name}";

            // Create role if it doesn't exist
            $role = DB::table('roles')->where('name', $newRoleName)->first();

            if (! $role) {
                $roleId = DB::table('roles')->insertGetId([
                    'name' => $newRoleName,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Copy permissions from old role
                $oldRole = DB::table('roles')->where('name', $assignment->role_name)->first();
                if ($oldRole) {
                    $permissions = DB::table('role_has_permissions')
                        ->where('role_id', $oldRole->id)
                        ->get();

                    foreach ($permissions as $perm) {
                        DB::table('role_has_permissions')->insert([
                            'permission_id' => $perm->permission_id,
                            'role_id' => $roleId,
                        ]);
                    }
                }
            } else {
                $roleId = $role->id;
            }

            // Reassign user to new role (remove old, add new)
            DB::table('model_has_roles')
                ->where('model_id', $assignment->user_id)
                ->where('model_type', 'App\\Models\\User')
                ->where('role_id', DB::table('roles')->where('name', $assignment->role_name)->value('id'))
                ->delete();

            // Check if user already has the new role
            $alreadyHas = DB::table('model_has_roles')
                ->where('model_id', $assignment->user_id)
                ->where('role_id', $roleId)
                ->where('model_type', 'App\\Models\\User')
                ->exists();

            if (! $alreadyHas) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $roleId,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $assignment->user_id,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Remove department-scoped roles and reassign users back to original roles
        $scopedRoles = DB::table('roles')
            ->where('name', 'like', '% - %')
            ->where('name', '!=', 'Super Admin')
            ->get();

        foreach ($scopedRoles as $role) {
            $originalName = explode(' - ', $role->name)[0];

            // Find users with this scoped role
            $users = DB::table('model_has_roles')
                ->where('role_id', $role->id)
                ->where('model_type', 'App\\Models\\User')
                ->get();

            $originalRole = DB::table('roles')->where('name', $originalName)->first();

            if ($originalRole) {
                foreach ($users as $user) {
                    DB::table('model_has_roles')
                        ->where('model_id', $user->model_id)
                        ->where('role_id', $role->id)
                        ->where('model_type', 'App\\Models\\User')
                        ->delete();

                    $alreadyHas = DB::table('model_has_roles')
                        ->where('model_id', $user->model_id)
                        ->where('role_id', $originalRole->id)
                        ->where('model_type', 'App\\Models\\User')
                        ->exists();

                    if (! $alreadyHas) {
                        DB::table('model_has_roles')->insert([
                            'role_id' => $originalRole->id,
                            'model_type' => 'App\\Models\\User',
                            'model_id' => $user->model_id,
                        ]);
                    }
                }
            }

            // Remove permissions from scoped role
            DB::table('role_has_permissions')->where('role_id', $role->id)->delete();
        }

        // Delete scoped roles
        DB::table('roles')
            ->where('name', 'like', '% - %')
            ->where('name', '!=', 'Super Admin')
            ->delete();
    }
};
