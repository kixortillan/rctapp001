<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRole extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_wa_user_role';

    protected $fillable = ['role_id', 'user_id'];

    protected $dates = ['deleted_at'];

}
