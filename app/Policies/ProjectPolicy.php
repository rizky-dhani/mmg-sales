<?php

namespace App\Policies;

use App\Models\User;

class ProjectPolicy extends BasePolicy
{
    protected string $model = 'project';

    /**
     * Determine whether the user can update the model.
     * Staff can only update their own projects.
     * Supervisors+ can oversee but cannot modify subordinate records.
     */
    public function update(User $user, $model): bool
    {
        // Must have update permission AND be the creator
        if (! $user->hasPermissionTo("update_{$this->model}")) {
            return false;
        }

        return $model->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Staff can only delete their own projects.
     * Supervisors+ can oversee but cannot delete subordinate records.
     */
    public function delete(User $user, $model): bool
    {
        // Must have delete permission AND be the creator
        if (! $user->hasPermissionTo("delete_{$this->model}")) {
            return false;
        }

        return $model->created_by === $user->id;
    }

    public function addCollaborator(User $user, $project): bool
    {
        return $this->isCreator($user, $project);
    }

    public function removeCollaborator(User $user, $project): bool
    {
        return $this->isCreator($user, $project);
    }

    private function isCreator(User $user, $project): bool
    {
        return $project->created_by === $user->id;
    }
}
