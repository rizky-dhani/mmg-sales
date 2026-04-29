<?php

namespace App\Filament\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait HasVisibilityScope
{
    /**
     * Apply visibility scope based on user role and hierarchy.
     * Staff: own records only
     * Supervisor: oversee subordinate records (view-only)
     * RSM/ASM: oversee their territory/position hierarchy
     * Director: oversee all Manager (RSM/ASM) and below records (view-only)
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

        // Director - can see all records from Manager (RSM/ASM) and below
        if ($user->hasRole('Director')) {
            $salesTeamIds = self::getSalesTeamUserIds();

            return $query->whereIn($userColumn, $salesTeamIds);
        }

        // Staff - can only see their own records
        if ($user->hasRole('Staff')) {
            return $query->where($userColumn, $user->id);
        }

        // Manager, Supervisor, RSM, ASM - oversee subordinate records
        if ($user->hasRole(['Manager', 'Supervisor', 'Regional Sales Manager', 'Area Sales Manager'])) {
            $subordinateIds = self::getSubordinateUserIds($user);

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
     * Staff: only own records
     * Supervisor+: oversee but cannot modify subordinate records
     * Director: view-only, cannot modify any records
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
        if ($user->hasRole('Director')) {
            return false;
        }

        // Staff, Manager, Supervisor, RSM, ASM can only modify their own records
        if ($user->hasRole(['Staff', 'Manager', 'Supervisor', 'Regional Sales Manager', 'Area Sales Manager'])) {
            return $record->{$userColumn} === $user->id;
        }

        return false;
    }

    /**
     * Get IDs of all subordinate users based on position hierarchy and territory.
     */
    private static function getSubordinateUserIds($user): array
    {
        $subordinateIds = [];

        // Get users with positions that report to this user's position
        if ($user->position) {
            $subordinatePositionIds = $user->position->children()->pluck('id')->toArray();
            $positionUserIds = User::whereIn('position_id', $subordinatePositionIds)
                ->pluck('id')
                ->toArray();
            $subordinateIds = array_merge($subordinateIds, $positionUserIds);
        }

        // Get users in subordinate territories
        if ($user->territory) {
            $descendantTerritoryIds = $user->territory->getAllDescendantIds();
            $territoryUserIds = User::whereIn('territory_id', $descendantTerritoryIds)
                ->pluck('id')
                ->toArray();
            $subordinateIds = array_merge($subordinateIds, $territoryUserIds);
        }

        // Get direct subordinates (users who have this user as manager)
        $directSubordinateIds = User::where('manager_id', $user->id)
            ->pluck('id')
            ->toArray();
        $subordinateIds = array_merge($subordinateIds, $directSubordinateIds);

        return array_unique($subordinateIds);
    }

    /**
     * Get IDs of all sales team users (Staff, Manager, Supervisor, RSM, ASM).
     * Used by Director to oversee all sales records.
     */
    private static function getSalesTeamUserIds(): array
    {
        $salesRoles = ['Staff', 'Manager', 'Supervisor', 'Regional Sales Manager', 'Area Sales Manager'];

        return User::role($salesRoles)
            ->pluck('id')
            ->toArray();
    }
}
