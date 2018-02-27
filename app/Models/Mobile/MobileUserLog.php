<?php

namespace App\Models\Mobile;

use App\Models\Mobile\Loan\BenefitType;
use App\Models\Mobile\MobileUser;
use App\Models\Mobile\ServiceType;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class MobileUserLog extends Model
{

    protected $table = 'tbl_mobile_user_logs';

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(MobileUser::class, 'mobile_user_id', 'id');
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_id', 'id');
    }

    public function benefitType()
    {
        return $this->belongsTo(BenefitType::class, 'loan_benefit_type_id', 'id');
    }

    public function getMonthFullName()
    {
        if (!empty($this->month)) {
            $date = DateTime::createFromFormat('!m', $this->month);
            return $date->format('F');
        }

        throw new BadMethodCallException('Model does not have a month property.');
    }

    public function getShortMonthNameAttribute()
    {
        if (!empty($this->month)) {
            $date = DateTime::createFromFormat('!m', $this->month);
            return $date->format('M');
        }

        throw new BadMethodCallException('Model does not have a month property.');
    }
}
