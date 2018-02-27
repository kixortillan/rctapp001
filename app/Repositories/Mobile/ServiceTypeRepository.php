<?php

namespace App\Repositories\Mobile;

use App\Models\Mobile\ServiceType;
use App\Repositories\Mobile\Contracts\ServiceTypeRepositoryInterface;
use DB;

class ServiceTypeRepository implements ServiceTypeRepositoryInterface
{

    public function allServices()
    {
        return ServiceType::where('is_deleted', false)
            ->get();
    }

    public function findServices(array $serviceIds)
    {
        return ServiceType::whereIn('id', $serviceIds)->get();
    }

    public function countTransactionInDateRange(int $serviceId, $startDate, $endDate)
    {
        $query = "SELECT IFNULL(logs.count, 0) as count,
                    months.month, logs.code
                    FROM (SELECT count(tbl_mobile_user_logs.service_id) AS count,
                    tbl_services.code,
                    month(tbl_mobile_user_logs.date_created) AS month
                    FROM tbl_services
                    LEFT JOIN tbl_mobile_user_logs
                    ON tbl_services.id = tbl_mobile_user_logs.service_id
                    WHERE tbl_mobile_user_logs.date_created >= ?
                    AND tbl_mobile_user_logs.date_created <= ?
                    AND tbl_services.id = ?
                    GROUP BY month, tbl_services.code, tbl_mobile_user_logs.service_id) logs
                    right JOIN (
                    select month from (SELECT 1 AS month
                    UNION SELECT 2 AS month
                    UNION SELECT 3 AS month
                    UNION SELECT 4 AS month
                    UNION SELECT 5 AS month
                    UNION SELECT 6 AS month
                    UNION SELECT 7 AS month
                    UNION SELECT 8 AS month
                    UNION SELECT 9 AS month
                    UNION SELECT 10 AS month
                    UNION SELECT 11 AS month
                    UNION SELECT 12) months
                    where month >= month(?)
                    and month <= month(?)) months
                    on logs.month = months.month
                    ORDER BY months.month asc";

        return collect(DB::select($query, [$startDate, $endDate, $serviceId, $startDate, $endDate]));
    }

    public function subTotalPerTransactionsInDateRange(array $serviceIds, $startDate, $endDate)
    {
        $tempString = str_repeat('?,', count($serviceIds));
        $bindingArrayString = substr($tempString, 0, strlen($tempString) - 1);

        $query = "SELECT id, code, ifnull(count, 0) as subtotal
                    FROM tbl_services
                    LEFT JOIN
                    (SELECT count(tbl_mobile_user_logs.service_id) AS count, service_id
                    FROM tbl_mobile_user_logs
                    WHERE tbl_mobile_user_logs.date_created >= ?
                    AND tbl_mobile_user_logs.date_created <= ?
                    AND tbl_mobile_user_logs.service_id in ({$bindingArrayString})
                    GROUP BY service_id) logs
                    on tbl_services.id = logs.service_id
                    WHERE tbl_services.id IN ({$bindingArrayString})";

        return collect(DB::select($query, array_merge([$startDate, $endDate], $serviceIds, $serviceIds)));
    }

    public function subTotalOfTransPerMonthInDateRange(array $serviceIds, $startDate, $endDate)
    {
        $tempString = str_repeat('?,', count($serviceIds));
        $bindingArrayString = substr($tempString, 0, strlen($tempString) - 1);

        $query = "SELECT ifnull(logs.count, 0) AS subtotal, months.month
                    FROM
                    (SELECT count(service_id) AS count, month(date_created) AS month
                    FROM tbl_mobile_user_logs
                    WHERE date_created >= ?
                    AND date_created <= ?
                    AND service_id IN ({$bindingArrayString})
                    GROUP BY month(date_created)) logs
                    RIGHT JOIN (
                    SELECT month FROM (SELECT 1 AS month
                    UNION SELECT 2 AS month
                    UNION SELECT 3 AS month
                    UNION SELECT 4 AS month
                    UNION SELECT 5 AS month
                    UNION SELECT 6 AS month
                    UNION SELECT 7 AS month
                    UNION SELECT 8 AS month
                    UNION SELECT 9 AS month
                    UNION SELECT 10 AS month
                    UNION SELECT 11 AS month
                    UNION SELECT 12) months
                    WHERE month >= month(?)
                    AND month <= month(?)) months
                    ON logs.month = months.month
                    ORDER BY months.month ASC";

        return collect(DB::select($query, array_merge(
            [$startDate, $endDate], $serviceIds, [$startDate, $endDate])));

    }

}
