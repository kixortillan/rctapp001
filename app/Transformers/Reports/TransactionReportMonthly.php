<?php

namespace App\Transformers\Reports;

use League\Fractal\TransformerAbstract;

class TransactionReportMonthlyTransformer extends TransformerAbstract
{
    public function transform($record)
    {
        return [
            'month' => $record->month,
            'total' => $record->count,
        ];
    }
}
