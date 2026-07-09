<?php

namespace App\Policies;

use App\Models\User;

class ActivityCommentPolicy extends BasePolicy
{
    protected string $model = 'activity_comment';

    public function update(User $user, $model): bool
    {
        if (! $user->hasPermissionTo("update_{$this->model}")) {
            return false;
        }

        return $model->user_id === $user->id;
    }

    public function delete(User $user, $model): bool
    {
        if (! $user->hasPermissionTo("delete_{$this->model}")) {
            return false;
        }

        return $model->user_id === $user->id;
    }
}
