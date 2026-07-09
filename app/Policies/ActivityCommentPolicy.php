<?php

namespace App\Policies;

use App\Models\User;

class ActivityCommentPolicy extends BasePolicy
{
    protected string $model = 'activity_comment';

    /**
     * Users can view any comment if they have permission.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo("view_any_{$this->model}");
    }

    /**
     * Users can view a comment if they have permission.
     */
    public function view(User $user, $model): bool
    {
        return $user->hasPermissionTo("view_{$this->model}");
    }

    /**
     * Users can create comments if they have permission.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo("create_{$this->model}");
    }

    /**
     * Users can only update their own comments.
     */
    public function update(User $user, $model): bool
    {
        if (! $user->hasPermissionTo("update_{$this->model}")) {
            return false;
        }

        return $model->user_id === $user->id;
    }

    /**
     * Users can only delete their own comments.
     */
    public function delete(User $user, $model): bool
    {
        if (! $user->hasPermissionTo("delete_{$this->model}")) {
            return false;
        }

        return $model->user_id === $user->id;
    }

    /**
     * Users can restore comments if they have permission.
     */
    public function restore(User $user, $model): bool
    {
        return $user->hasPermissionTo("restore_{$this->model}");
    }

    /**
     * Users can force delete comments if they have permission.
     */
    public function forceDelete(User $user, $model): bool
    {
        return $user->hasPermissionTo("force_delete_{$this->model}");
    }
}
