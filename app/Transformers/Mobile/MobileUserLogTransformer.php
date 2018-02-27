<?php

namespace App\Transformers\Mobile;

use App\Models\Mobile\MobileUserLog;
use League\Fractal\TransformerAbstract;

class MobileUserLogTransformer extends TransformerAbstract
{

    public function transform(MobileUserLog $log)
    {
        return [
            'id' => (int) $log->id,
            'mobile_user_id' => (int) $log->mobile_user_id,
            'loan_benefit_type_id' => (int) $log->loan_benefit_type_id,
            'service_id' => (int) $log->service_id,
            'service_name' => $log->service,
        ];
    }
}
