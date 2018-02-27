<?php

namespace App;

use App\Models\Core\Role;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The name of the table in database
     *
     * @var string
     */
    protected $table = 'tbl_wa_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'first_name', 'middle_name',
        'last_name', 'avatar', 'mobile_number', 'verified',
        'verify_token', 'token_expires',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'verified',
        'verify_token', 'token_expires',
    ];

    public function findForPassport($username)
    {
        return $this->where('username', $username)
            ->where('verified', true)->first();
    }

    public function roles()
    {
        return $this->belongsToMany(
            Role::class, 'tbl_wa_user_role',
            'user_id', 'role_id'
        )->withTimestamps();
    }

    public function getFullNameAttribute()
    {
        $fullName = implode(array_filter([
            $this->first_name,
            substr($this->middle_name, 0, 1),
            $this->last_name,
        ]), ' ');

        return ucwords($fullName);
    }

    public function getMiddleInitialAttribute()
    {
        return ucwords(substr($this->middle_name, 0, strlen($this->middle_name)));
    }

    public function getFirstNameAttribute($val)
    {
        return ucwords($val);
    }

    public function getLastNameAttribute($val)
    {
        return ucwords($val);
    }

    public function getAvatarAttribute($val)
    {
        return !empty($val) ? asset("storage/{$val}") :
        asset('images/avatar.png');
    }
}
