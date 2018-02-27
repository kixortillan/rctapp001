<?php

namespace App\Transformers\User;

use App\Models\Core\Role;
use League\Fractal\TransformerAbstract;

class RoleTransformer extends TransformerAbstract
{

    public function transform(Role $role)
    {
        return [
            'id' => (int) $role->id,
            'role' => $role->name,
            'desc' => $role->desc,
        ];
    }

}
