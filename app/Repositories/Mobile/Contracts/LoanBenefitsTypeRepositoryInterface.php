<?php

namespace App\Repositories\Mobile\Contracts;

interface LoanBenefitsTypeRepositoryInterface
{
    function countLoansAndBenefitsApplication($startDate, $endDate);
}
