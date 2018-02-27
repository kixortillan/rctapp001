<?php

namespace App\Transformers\User;

use App\Models\Core\UserRole;
use League\Fractal\TransformerAbstract;

class UserRoleTransformer extends TransformerAbstract
{

    public function transform(UserRole $userRole)
    {
        return [
            'id' => (int) $userRole->id,
            'role_id' => $userRole->role_id,
            'user_id' => $userRole->user_id,
            'created_at' => $userRole->created_at,
            'updated_at' => $userRole->updated_at,
        ];
    }

}
