<?php

namespace App\Transformers\Reports;

use League\Fractal\TransformerAbstract;

class TransactionReportYearlyTransformer extends TransformerAbstract
{
    public function transform($record)
    {
        return [
            'year' => $record->year,
            'total' => $record->count,
        ];
    }
}
