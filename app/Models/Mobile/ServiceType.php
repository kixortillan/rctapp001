<?php

namespace App\Models\Mobile;

use App\Models\Mobile\mobileUserLogs;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $table = 'tbl_services';

    public function mobileUserLogs()
    {
        return $this->hasMany(mobileUserLogs::class);
    }
}
