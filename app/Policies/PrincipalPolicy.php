<?php

namespace App\Policies;

use App\Models\User;

class PrincipalPolicy extends BasePolicy
{
    protected string $model = 'principal';

    /**
     * Check if user is Admin from Import & Purchasing department
     */
    protected function isAdminFromImportPurchasing(User $user): bool
    {
        return $user->hasBaseRole('Admin') &&
               $user->department?->name === 'Import & Purchasing';
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
        return $this->isAdminFromImportPurchasing($user);
    }

    public function view(User $user, $model): bool
    {
        return $this->isAdminFromImportPurchasing($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdminFromImportPurchasing($user);
    }

    public function update(User $user, $model): bool
    {
        return $this->isAdminFromImportPurchasing($user);
    }

    public function delete(User $user, $model): bool
    {
        return $this->isAdminFromImportPurchasing($user);
    }

    public function restore(User $user, $model): bool
    {
        return $this->isAdminFromImportPurchasing($user);
    }

    public function forceDelete(User $user, $model): bool
    {
        return $this->isAdminFromImportPurchasing($user);
    }
}
