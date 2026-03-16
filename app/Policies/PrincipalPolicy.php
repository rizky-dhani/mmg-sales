<?php

namespace App\Policies;

use App\Models\User;

class PrincipalPolicy extends BasePolicy
{
    protected string $model = 'principal';

    /**
     * Check if user is Supervisor from Import & Purchasing department
     */
    protected function isSupervisorFromImportPurchasing(User $user): bool
    {
        return $user->hasRole('Supervisor') &&
               $user->department?->name === 'Import & Purchasing';
    }

    /**
     * Check if user can manage principals
     */
    protected function canManage(User $user): bool
    {
        return $this->isSupervisorFromImportPurchasing($user);
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
        return $this->canManage($user) || $user->hasPermissionTo("view_any_{$this->model}");
    }

    public function view(User $user, $model): bool
    {
        return $this->canManage($user) || $user->hasPermissionTo("view_{$this->model}");
    }

    public function create(User $user): bool
    {
        return $this->canManage($user) || $user->hasPermissionTo("create_{$this->model}");
    }

    public function update(User $user, $model): bool
    {
        return $this->canManage($user) || $user->hasPermissionTo("update_{$this->model}");
    }

    public function delete(User $user, $model): bool
    {
        return $this->canManage($user) || $user->hasPermissionTo("delete_{$this->model}");
    }

    public function restore(User $user, $model): bool
    {
        return $this->canManage($user) || $user->hasPermissionTo("restore_{$this->model}");
    }

    public function forceDelete(User $user, $model): bool
    {
        return $this->canManage($user) || $user->hasPermissionTo("force_delete_{$this->model}");
    }
}
