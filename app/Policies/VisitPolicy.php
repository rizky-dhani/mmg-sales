<?php

namespace App\Policies;

use App\Models\User;

class VisitPolicy extends BasePolicy
{
    protected string $model = 'visit';
}
