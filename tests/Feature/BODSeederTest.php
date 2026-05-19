<?php

use App\Models\Department;
use App\Models\Position;
use App\Models\Role;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

it('seeds board of director role with only view permissions', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $role = Role::where('name', 'Board of Director')->first();

    expect($role)->not->toBeNull()
        ->and($role->permissions)->not->toBeEmpty();

    // Verify all permissions assigned to BOD start with 'view_'
    foreach ($role->permissions as $permission) {
        expect($permission->name)->toStartWith('view');
    }

    // Verify BOD does NOT have any create/update/delete permissions
    $nonViewPermissions = Permission::where('name', 'not like', 'view_%')->get();
    foreach ($nonViewPermissions as $permission) {
        expect($role->hasPermissionTo($permission))->toBeFalse();
    }
});

it('seeds mgmt department and bod position correctly', function () {
    $this->seed([
        DepartmentSeeder::class,
        PositionSeeder::class,
    ]);

    $department = Department::where('code', 'MGMT')->first();
    expect($department)->not->toBeNull()
        ->and($department->name)->toBe('Management');

    $position = Position::where('code', 'BOD')->first();
    expect($position)->not->toBeNull()
        ->and($position->name)->toBe('Board of Director')
        ->and($position->level)->toBe(0)
        ->and($position->department_id)->toBe($department->id);

    $head = Position::where('code', 'HEAD')->first();
    expect($head->parent_id)->toBe($position->id);
});
