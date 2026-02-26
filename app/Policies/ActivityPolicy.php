<?php

namespace App\Policies;

use App\Models\User;

class ActivityPolicy extends BasePolicy
{
    protected string $model = 'activity';

    public function createForProject(User $user, $project): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($project->created_by === $user->id) {
            return true;
        }

        return $project->collaborators()->where('user_id', $user->id)->exists();
    }
}
