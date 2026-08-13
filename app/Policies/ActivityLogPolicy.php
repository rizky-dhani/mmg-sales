<?php

namespace App\Policies;

use App\Models\User;

class ActivityLogPolicy extends BasePolicy
{
    protected string $model = 'activity_log';

    protected array $authorizedRoles = ['Super Admin'];

    /**
     * Activity logs are read-only. Only Super Admin can access.
     * All write operations are denied.
     */
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
