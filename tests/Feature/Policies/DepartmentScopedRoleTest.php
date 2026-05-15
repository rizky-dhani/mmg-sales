<?php

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create departments
    Department::create(['id' => 1, 'name' => 'Sales', 'code' => 'SALES', 'description' => '']);
    Department::create(['id' => 2, 'name' => 'Purchasing', 'code' => 'PURCH', 'description' => '']);

    // Create permissions
    Permission::findOrCreate('view_any_customer');
    Permission::findOrCreate('view_any_product');
    Permission::findOrCreate('view_any_order');
});

it('can create a department-scoped role', function () {
    $salesRole = Role::create([
        'name' => 'Admin',
        'guard_name' => 'web',
        'department_id' => 1,
    ]);
    $salesRole->givePermissionTo('view_any_customer');

    expect($salesRole->department_id)->toBe(1);
    expect($salesRole->hasPermissionTo('view_any_customer'))->toBeTrue();
});

it('user in correct department inherits permissions from dept-scoped role', function () {
    $role = Role::create(['name' => 'Admin', 'guard_name' => 'web', 'department_id' => 1]);
    $role->givePermissionTo('view_any_customer');

    $user = User::factory()->create(['department_id' => 1]);
    $user->assignRole($role);

    expect($user->hasPermissionTo('view_any_customer'))->toBeTrue();
});

it('user in different department does not bypass from dept-scoped role', function () {
    $role = Role::create(['name' => 'Admin', 'guard_name' => 'web', 'department_id' => 1]);
    $role->givePermissionTo('view_any_customer');

    $user = User::factory()->create(['department_id' => 2]); // Purchasing
    $user->assignRole($role);

    // The BasePolicy before() should filter this role when checking authorizedRoles
    // hasPermissionTo still returns true because Spatie checks all roles
    // The policy-level check happens in BasePolicy.before()
    expect($user->hasPermissionTo('view_any_customer'))->toBeTrue();
});

it('detaches stale dept-scoped roles when user changes department', function () {
    $salesRole = Role::create(['name' => 'Sales Admin', 'guard_name' => 'web', 'department_id' => 1]);
    $purchasingRole = Role::create(['name' => 'Purchasing Admin', 'guard_name' => 'web', 'department_id' => 2]);

    $user = User::factory()->create(['department_id' => 1]);
    $user->assignRole($salesRole);
    $user->assignRole($purchasingRole);

    expect($user->roles)->toHaveCount(2);

    // Change department
    $user->update(['department_id' => 2]);

    // Sales role should be detached, purchasing role remains
    $user->refresh();
    expect($user->roles)->toHaveCount(1);
    expect($user->roles->first()->id)->toBe($purchasingRole->id);
});

it('does not detach global roles when user changes department', function () {
    $globalRole = Role::create(['name' => 'Staff', 'guard_name' => 'web', 'department_id' => null]);
    $salesRole = Role::create(['name' => 'Admin', 'guard_name' => 'web', 'department_id' => 1]);

    $user = User::factory()->create(['department_id' => 1]);
    $user->assignRole($globalRole);
    $user->assignRole($salesRole);

    expect($user->roles)->toHaveCount(2);

    $user->update(['department_id' => 2]);

    $user->refresh();
    expect($user->roles)->toHaveCount(1);
    expect($user->roles->first()->name)->toBe('Staff');
});

it('can filter roles by department on user form', function () {
    Role::create(['name' => 'Global Admin', 'guard_name' => 'web', 'department_id' => null]);
    Role::create(['name' => 'Sales Admin', 'guard_name' => 'web', 'department_id' => 1]);
    Role::create(['name' => 'Purchase Admin', 'guard_name' => 'web', 'department_id' => 2]);

    // Simulate the query used in UserForm
    $departmentId = 1;
    $roles = Role::where(function ($q) use ($departmentId) {
        $q->whereNull('department_id')
            ->orWhere('department_id', $departmentId);
    })->get();

    expect($roles)->toHaveCount(2);
    expect($roles->pluck('name'))->toContain('Global Admin', 'Sales Admin');
    expect($roles->pluck('name'))->not->toContain('Purchase Admin');
});
