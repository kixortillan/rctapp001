<?php

namespace App\Repositories\Core\Contracts;

use App\Utilities\Pagination\Contracts\PagerInterface;

interface UserRepositoryInterface
{
    function paginateUsersWithRoles(array $roles, PagerInterface $pager);

    function createUser(array $props);

    function userById(int $id);

    function userByUsername(string $username);

    function userByEmail(string $email);

    function updateProfilePic(int $userId, string $path);

}
