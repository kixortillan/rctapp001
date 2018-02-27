<?php

namespace App\Repositories\Core\Contracts;

interface UserRoleRepositoryInterface
{
    function removeRolesForUser(int $userId, array $roleIds = []);

    function rolesByUserId(int $userId);

    function addRolesForUser(int $userId, array $roleIds);

    function restoreDeletedRolesForUser(int $userId, array $roleIds);

    function rolesByUserIdWithDeleted(int $userId);
}
