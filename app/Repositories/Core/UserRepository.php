<?php

namespace App\Repositories\Core;

use App\Repositories\Core\Contracts\UserRepositoryInterface;
use App\User;
use App\Utilities\Pagination\Contracts\PagerInterface;

class UserRepository implements UserRepositoryInterface
{
    public function paginateUsersWithRoles(array $roles, PagerInterface $pager)
    {
        $query = User::join('tbl_wa_user_role', function ($join) {
            $join->on('tbl_wa_users.id', '=', 'tbl_wa_user_role.user_id');
        })->whereIn('tbl_wa_user_role.role_id', $roles);

        if ($pager->orderBy()) {
            $query->orderBy('tbl_wa_user_role' . '.' . $pager->orderBy(), $pager->order());
        }

        if ($pager->search()) {
            foreach ($pager->searchColumns() as $col) {

                $query->where($col, '=', '%' . $pager->search() . '%');

            }
        }

        $query->offset(($pager->page() - 1) * $pager->perPage());
        $query->limit($pager->perPage());

        return $query->get();
    }

    public function createUser(array $props)
    {
        return User::create($props);
    }

    public function updateProfilePic(int $userId, string $path)
    {
        $user = User::find($userId);
        $user->avatar = $path;
        $user->save();
        return $user;
    }

    public function userById(int $id)
    {
        return User::where('id', $id)->first();
    }

    public function userByUsername(string $username)
    {
        return User::where('username', $username)->first();
    }

    public function userByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function userByVerifyToken(string $token)
    {
        return User::where('verify_token', $token)->first();
    }

    public function userByEmailCredentials(string $email, $password)
    {
        return User::where('email', $email)
            ->where('password', $password)
            ->where('verified', true)
            ->first();
    }

    public function updateUserById(int $id, array $data)
    {
        return User::where('id', $id)
            ->update($data);
    }

    public function createResetToken(int $userId, string $token)
    {
        return User::where('id', $userId)->update([
            'reset_token' => $token,
        ]);
    }

    public function rolesByUser(int $userId)
    {
        return User::where('id', $userId)->with(['roles' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])->first();
    }
}
