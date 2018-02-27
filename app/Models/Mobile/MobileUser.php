<?php

namespace App\Models\Mobile;

use App\Models\Mobile\MobileUserLog;
use Illuminate\Database\Eloquent\Model;

class MobileUser extends Model {

	protected $table = 'tbl_mobile_users';

	public $timestamps = false;

	public function mobileUserLogs() {
		return $this->hasMany(MobileUserLog::class);
	}
}
