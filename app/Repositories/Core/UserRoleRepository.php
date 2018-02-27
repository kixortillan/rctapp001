<?php

namespace App\Repositories\Core;

use App\Models\Core\UserRole;
use App\Repositories\Core\Contracts\UserRoleRepositoryInterface;
use Carbon\Carbon;

class UserRoleRepository implements UserRoleRepositoryInterface
{
    public function removeRolesForUser(int $userId, array $roleIds = [])
    {
        $query = UserRole::where('user_id', $userId);

        if (!empty($roleIds)) {
            $query->whereIn('role_id', $roleIds);
        }

        return $query->delete();
    }

    public function rolesByUserId(int $userId)
    {
        return UserRole::where('user_id', $userId)->get();
    }

    public function rolesByUserIdWithDeleted(int $userId)
    {
        return UserRole::withTrashed()->where('user_id', $userId)->get();
    }

    public function addRolesForUser(int $userId, array $roleIds)
    {
        $data = [];
        foreach ($roleIds as $roleId) {
            $data[] = [
                'user_id' => (int) $userId,
                'role_id' => (int) $roleId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        return UserRole::insert($data);
    }

    public function restoreDeletedRolesForUser(int $userId, array $roleIds)
    {
        return UserRole::whereIn('role_id', $roleIds)
            ->where('user_id', $userId)
            ->restore();
    }

    public function countTotalUsers(int $roleId)
    {
        return UserRole::where('role_id', $roleId)
            ->count();
    }
}
