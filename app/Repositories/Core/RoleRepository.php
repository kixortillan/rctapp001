<?php

namespace App\Repositories\Core;

use App\Models\Core\Role;
use App\Repositories\Core\Contracts\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{
    public function allRoles()
    {
        return Role::orderBy('created_at', 'desc')->get();
    }
}
