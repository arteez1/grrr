<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool
    {
        return $user->role->name === Role::ADMIN;
    }

    public function create(User $user): bool
    {
        return $user->role->name === Role::ADMIN;
    }

    public function delete(User $user): bool
    {
        return $user->role->name === Role::ADMIN;
    }
}
