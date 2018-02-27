<?php

namespace App\Repositories\Mobile\Contracts;

use Carbon\Carbon;

interface MobileUserLogsRepositoryInterface
{
    function distinctInquiriesPerHour(array $serviceIds, $year, $month, $day);

    function distinctInquiriesPerDay(array $serviceIds, $year, $month);

    function distinctInquiriesPerMonth(array $serviceIds, $year);

    function distinctInquiriesPerYear(array $serviceIds, $startYear, $endYear);

    function countInquiriesPerService(int $serviceId, Carbon $dateFrom, Carbon $dateTo);
}
