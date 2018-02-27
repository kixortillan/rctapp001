<?php

namespace App\Models\Mobile\Loan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Models\Mobile\MobileUserLog;

class BenefitType extends Model {
	//
	protected $table = 'tbl_loan_benefits_type';

	public $timestamps = false;

	public function mobileUserLogs() {
		return $this->hasMany(MobileUserLog::class);
	}
}
