<?php

namespace App\Repositories\Core\Contracts;

interface PasswordResetRepositoryInterface
{
    function saveToken(string $email, string $token);

    function findUsingToken(string $token);
}
