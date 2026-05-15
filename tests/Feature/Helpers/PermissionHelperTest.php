<?php

use App\Helpers\PermissionHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create sample permissions
    $actions = ['view', 'view_any', 'create', 'update', 'delete'];
    foreach (['customer', 'order', 'customer_group'] as $model) {
        foreach ($actions as $action) {
            Permission::findOrCreate("{$action}_{$model}");
        }
    }
});

it('gets models list', function () {
    $models = PermissionHelper::getModels();

    expect($models)->toBeArray();
    expect($models)->toContain('customer', 'order', 'user');
});

it('parses simple permission names', function () {
    $result = PermissionHelper::parsePermissionName('view_any_customer');

    expect($result)->toBe([
        'action' => 'view_any',
        'model' => 'customer',
    ]);
});

it('parses composite model permission names', function () {
    $result = PermissionHelper::parsePermissionName('create_customer_group');

    expect($result)->toBe([
        'action' => 'create',
        'model' => 'customer_group',
    ]);
});

it('generates model permissions', function () {
    $permissions = PermissionHelper::generateModelPermissions('customer');

    expect($permissions)->toContain('view_customer');
    expect($permissions)->toContain('create_customer');
    expect($permissions)->toContain('delete_customer');
});

it('returns human-readable model label', function () {
    expect(PermissionHelper::getModelLabel('customer_group'))->toBe('Customer Group');
    expect(PermissionHelper::getModelLabel('user'))->toBe('User');
});

it('returns human-readable action label', function () {
    expect(PermissionHelper::getActionLabel('view_any'))->toBe('View Any');
    expect(PermissionHelper::getActionLabel('force_delete'))->toBe('Force Delete');
});

it('groups permissions by model', function () {
    $grouped = PermissionHelper::getGroupedOptions();

    expect($grouped)->toHaveKey('Customer');
    expect($grouped)->toHaveKey('Order');
    expect($grouped)->toHaveKey('Customer Group');
});

it('returns null for unparseable permission names', function () {
    $result = PermissionHelper::parsePermissionName('invalid');

    expect($result)->toBeNull();
});
