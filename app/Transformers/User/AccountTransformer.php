<?php

namespace App\Transformers\User;

use App\User;
use League\Fractal\TransformerAbstract;

class AccountTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'roles',
    ];

    public function transform(User $user)
    {
        return [
            'id' => (int) $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'full_name' => $user->full_name,
            'avatar' => $user->avatar,
            'mobile_number' => $user->mobile_number,
            'date_registered' => $user->verified_at,
        ];
    }

    /**
     * [includeRoles description]
     * @param  User   $user [description]
     * @return [type]       [description]
     */
    public function includeRoles(User $user)
    {
        $roles = $user->roles;

        return $this->collection($roles, new RoleTransformer);
    }
}
