<?php

namespace App\Policies;

class VisitPolicy extends BasePolicy
{
    protected string $model = 'visit';

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
