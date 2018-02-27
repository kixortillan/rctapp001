<?php

namespace App\Repositories\Mobile;

use App\Repositories\Mobile\Contracts\LoanBenefitsTypeRepositoryInterface;
use DB;

class LoanBenefitsTypeRepository implements LoanBenefitsTypeRepositoryInterface
{

    public function countLoansAndBenefitsApplication($startDate, $endDate)
    {
        $query = "SELECT IFNULL(logs.count, 0) AS total,
                logs.loan_benefit_type_id, lbt.id, lbt.type
                FROM (SELECT count(id) AS count, loan_benefit_type_id
                FROM tbl_mobile_user_logs
                WHERE tbl_mobile_user_logs.service_id = 3
                AND tbl_mobile_user_logs.date_created >= ?
                AND tbl_mobile_user_logs.date_created <= ?
                GROUP BY loan_benefit_type_id) logs
                RIGHT JOIN (SELECT id, type
                FROM tbl_loan_benefits_type
                WHERE is_deleted = 0) lbt
                ON logs.loan_benefit_type_id = lbt.id;";

        return DB::select($query, [$startDate, $endDate]);
    }

}
