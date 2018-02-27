<?php

namespace App\Models\Core;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_wa_roles';

    protected $fillable = ['name', 'desc'];

    protected $dates = ['deleted_at'];

    public function users()
    {
        return $this->belongsToMany(User::class,
            'tbl_wa_user_role', 'role_id', 'user_id')
            ->withTimestamps();
    }

    public function getNameAttribute($val)
    {
        return ucwords($val);
    }
}
