<?php

namespace App\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;

class PermissionHelper
{
    /**
     * Dynamically discovered model slugs from app/Models/.
     * Ordered longest-first for suffix matching.
     */
    private static ?Collection $models = null;

    /**
     * Available actions in the permission naming convention.
     */
    private static array $actions = [
        'view',
        'view_any',
        'create',
        'update',
        'delete',
        'restore',
        'force_delete',
    ];

    /**
     * Get permissions grouped by model for CheckboxList options.
     * Returns format: ['Customer' => ['view_any_customer' => 'View Any Customer', ...], ...]
     */
    public static function getGroupedOptions(): array
    {
        $permissions = Permission::all()->pluck('name');
        $groups = [];

        foreach ($permissions as $permission) {
            $parsed = self::parsePermissionName($permission);
            if ($parsed === null) {
                continue;
            }

            $modelLabel = self::getModelLabel($parsed['model']);
            $actionLabel = self::getActionLabel($parsed['action']);

            $groups[$modelLabel][$permission] = $actionLabel;
        }

        // Sort groups by model label
        ksort($groups);

        // Sort actions within each group by action order
        foreach ($groups as &$actions) {
            uksort($actions, function ($a, $b) {
                $orderA = array_search(explode('_', $a)[0].(str_contains($a, '_any') ? '_any' : ''), self::$actions);
                $orderB = array_search(explode('_', $b)[0].(str_contains($b, '_any') ? '_any' : ''), self::$actions);

                return ($orderA === false ? 999 : $orderA) <=> ($orderB === false ? 999 : $orderB);
            });
        }

        return $groups;
    }

    /**
     * Parse a permission name like 'view_any_customer' into ['action' => 'view_any', 'model' => 'customer'].
     * Returns null if parsing fails.
     */
    public static function parsePermissionName(string $permission): ?array
    {
        foreach (self::discoverModels() as $model) {
            if (str_ends_with($permission, '_'.$model)) {
                $prefix = substr($permission, 0, -(strlen($model) + 1));

                return [
                    'action' => $prefix,
                    'model' => $model,
                ];
            }
        }

        return null;
    }

    /**
     * Get all known model slugs from the filesystem.
     */
    public static function getModels(): array
    {
        return self::discoverModels()->values()->toArray();
    }

    private static function discoverModels(): Collection
    {
        if (self::$models === null) {
            self::$models = collect(File::files(app_path('Models')))
                ->map(fn ($file) => str($file->getFilename())->before('.php')->snake()->toString())
                ->sort(fn ($a, $b) => strlen($b) <=> strlen($a));
        }

        return self::$models;
    }

    /**
     * Generate all 7 permission names for a given model slug.
     */
    public static function generateModelPermissions(string $model): array
    {
        return array_map(fn (string $action) => "{$action}_{$model}", self::$actions);
    }

    /**
     * Convert a model slug to a human-readable label.
     * E.g., 'customer_group' => 'Customer Group', 'user' => 'User'
     */
    public static function getModelLabel(string $model): string
    {
        return str($model)->replace('_', ' ')->title()->toString();
    }

    /**
     * Convert an action slug to a human-readable label.
     * E.g., 'view_any' => 'View Any', 'force_delete' => 'Force Delete'
     */
    public static function getActionLabel(string $action): string
    {
        return str($action)->replace('_', ' ')->title()->toString();
    }
}
