<?php

namespace App\Policies;

use App\Models\User;

class MilestonePolicy extends BasePolicy
{
    protected string $model = 'milestone';

    /**
     * Only Super Admin can access Milestone
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return false; // Deny all other users
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, $model): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, $model): bool
    {
        return false;
    }

    public function delete(User $user, $model): bool
    {
        return false;
    }

    public function restore(User $user, $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, $model): bool
    {
        return false;
    }
}
