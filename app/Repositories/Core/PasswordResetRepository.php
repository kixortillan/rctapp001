<?php

namespace App\Repositories\Core;

use App\Models\Core\PasswordReset;
use App\Repositories\Core\Contracts\PasswordResetRepositoryInterface;

class PasswordResetRepository implements PasswordResetRepositoryInterface
{
    public function saveToken(string $email, string $token)
    {
        return PasswordReset::create([
            'email' => $email,
            'token' => $token,
        ]);
    }

    public function findUsingToken(string $token)
    {
        return PasswordReset::where('token', $token)
            ->first();
    }
}
