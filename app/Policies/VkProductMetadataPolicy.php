<?php

namespace App\Policies;


use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class VkProductMetadataPolicy
{
    use HandlesAuthorization;

    public function manage(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}
