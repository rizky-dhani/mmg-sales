<?php

namespace App\Policies;

use App\Models\User;

class TargetPolicy extends BasePolicy
{
    protected string $model = 'target';

    /**
     * Check if user is Director or Super Admin
     */
    protected function isDirectorOrSuperAdmin(User $user): bool
    {
        return $user->hasAnyBaseRole(['Director', 'Super Admin']);
    }

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasBaseRole('Director');
    }

    public function view(User $user, $model): bool
    {
        return $user->hasBaseRole('Director');
    }

    public function create(User $user): bool
    {
        return $user->hasBaseRole('Director');
    }

    public function update(User $user, $model): bool
    {
        return $user->hasBaseRole('Director');
    }

    public function delete(User $user, $model): bool
    {
        return $user->hasBaseRole('Director');
    }

    public function restore(User $user, $model): bool
    {
        return $user->hasBaseRole('Director');
    }

    public function forceDelete(User $user, $model): bool
    {
        return $user->hasBaseRole('Director');
    }
}
