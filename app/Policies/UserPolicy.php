<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends BasePolicy
{
    protected string $model = 'user';

    public function viewAny(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function view(User $user, $model): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function update(User $user, $model): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function delete(User $user, $model): bool
    {
        return $user->hasRole('Super Admin');
    }
}
