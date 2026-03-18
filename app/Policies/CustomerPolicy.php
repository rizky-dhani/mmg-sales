<?php

namespace App\Policies;

use App\Models\User;

class CustomerPolicy extends BasePolicy
{
    protected string $model = 'customer';

    /**
     * Check if user is Admin from Sales department
     */
    protected function isAdminFromSales(User $user): bool
    {
        return $user->hasRole('Admin') &&
               $user->department?->name === 'Sales';
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
        return $this->isAdminFromSales($user);
    }

    public function view(User $user, $model): bool
    {
        return $this->isAdminFromSales($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdminFromSales($user);
    }

    public function update(User $user, $model): bool
    {
        return $this->isAdminFromSales($user);
    }

    public function delete(User $user, $model): bool
    {
        return $this->isAdminFromSales($user);
    }

    public function restore(User $user, $model): bool
    {
        return $this->isAdminFromSales($user);
    }

    public function forceDelete(User $user, $model): bool
    {
        return $this->isAdminFromSales($user);
    }
}
