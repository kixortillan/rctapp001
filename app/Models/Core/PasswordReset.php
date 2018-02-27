<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = 'tbl_wa_password_resets';

    protected $fillable = [
        'email', 'token',
    ];

    const UPDATED_AT = null;
}
