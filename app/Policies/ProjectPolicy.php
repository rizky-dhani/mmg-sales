<?php

namespace App\Policies;

use App\Models\User;

class ProjectPolicy extends BasePolicy
{
    protected string $model = 'project';

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
