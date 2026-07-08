<?php

namespace App\Filament\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait HasVisibilityScope
{
    /**
     * Apply visibility scope based on position hierarchy (OR) and territory hierarchy (OR),
     * with direct reports (OR) included via manager_id.
     *
     * - Super Admin: sees everything
     * - Director: sees all sales team records (view-only)
     * - Staff: own records only
     * - Others: own records + subordinates (position OR territory union) + direct reports
     */
    public static function applyVisibilityScope(Builder $query, string $userColumn = 'user_id'): Builder
    {
        $user = auth()->user();

        if (! $user) {
            return $query;
        }

        // Super Admin bypasses all visibility restrictions
        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        // Director - can see all records from the sales team
        if ($user->hasBaseRole('Director')) {
            $salesTeamIds = self::getSalesTeamUserIds();

            return $query->whereIn($userColumn, $salesTeamIds);
        }

        // Staff - can only see their own records
        if ($user->hasBaseRole('Staff')) {
            return $query->where($userColumn, $user->id);
        }

        // Other users: resolve subordinates via position OR territory union,
        // plus direct reports via manager_id
        $subordinateIds = self::getSubordinateUserIds($user);

        if (! empty($subordinateIds)) {
            return $query->where(function ($q) use ($user, $userColumn, $subordinateIds) {
                $q->where($userColumn, $user->id) // Own records
                    ->orWhereIn($userColumn, $subordinateIds); // Subordinate records
            });
        }

        // Default: own records only
        return $query->where($userColumn, $user->id);
    }

    /**
     * Check if user can edit/delete a specific record.
     *
     * - Super Admin: can modify anything
     * - Director: view-only (cannot modify any records)
     * - Everyone else: can only modify their own records
     */
    public static function canModifyRecord($record, string $userColumn = 'user_id'): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        // Super Admin can modify anything
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Director is view-only, cannot modify any records
        if ($user->hasBaseRole('Director')) {
            return false;
        }

        // Everyone else: can only modify their own records
        return $record->{$userColumn} === $user->id;
    }

    /**
     * Get IDs of all subordinate users based on position OR territory hierarchy,
     * plus direct reports (manager_id).
     *
     * Logic:
     *   subordinateIds = (positionDescendants ∪ territoryDescendants) ∪ directReports
     *
     * Users in descendant positions OR descendant territories are both visible.
     */
    private static function getSubordinateUserIds($user): array
    {
        $userIds = [];

        // Users in descendant positions (full tree via parent_id hierarchy)
        if ($user->position) {
            $descendantPositionIds = $user->position->getAllDescendantIds();

            $positionUserIds = User::whereIn('position_id', $descendantPositionIds)
                ->where('id', '!=', $user->id)
                ->pluck('id')
                ->toArray();

            $userIds = array_merge($userIds, $positionUserIds);
        }

        // Users in descendant territories (full tree via parent_id hierarchy)
        if ($user->territory) {
            $descendantTerritoryIds = $user->territory->getAllDescendantIds();

            $territoryUserIds = User::whereIn('territory_id', $descendantTerritoryIds)
                ->where('id', '!=', $user->id)
                ->pluck('id')
                ->toArray();

            $userIds = array_merge($userIds, $territoryUserIds);
        }

        // Direct reports (users who have this user as manager)
        $directReportIds = User::where('manager_id', $user->id)
            ->pluck('id')
            ->toArray();

        return array_unique(array_merge($userIds, $directReportIds));
    }

    /**
     * Get IDs of all sales team users (Staff, Manager, Supervisor, RSM, ASM).
     * Used by Director to oversee all sales records.
     */
    private static function getSalesTeamUserIds(): array
    {
        $salesRoles = ['Staff', 'Manager', 'Supervisor', 'Regional Sales Manager', 'Area Sales Manager'];

        return User::whereHas('roles', function ($query) use ($salesRoles): void {
            $query->where(function ($q) use ($salesRoles): void {
                foreach ($salesRoles as $role) {
                    $q->orWhere('name', $role)->orWhere('name', 'like', "{$role} - %");
                }
            });
        })->pluck('id')->toArray();
    }
}
