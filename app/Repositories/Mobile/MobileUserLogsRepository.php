<?php

namespace App\Repositories\Mobile;

use App\Models\Mobile\MobileUserLog;
use App\Repositories\Mobile\Contracts\MobileUserLogsRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MobileUserLogsRepository implements MobileUserLogsRepositoryInterface
{
    public function distinctInquiriesPerHour(array $serviceIds, $year, $month, $day)
    {
        return MobileUserLog::whereRaw("day(date_created) = ?", [$day])
            ->whereRaw("month(date_created) = ?", [$month])
            ->whereRaw("year(date_created) = ?", [$year])
            ->whereIn("service_id", $serviceIds)
            ->groupBy(DB::raw("hour(date_created), service_id"))
            ->selectRaw("hour(date_created) as hour, count(*) as count, service_id")
            ->get();
    }

    public function distinctInquiriesPerDay(array $serviceIds, $year, $month)
    {
        return MobileUserLog::whereRaw("month(date_created) = ?", [$month])
            ->whereRaw("year(date_created) = ?", [$year])
            ->whereIn("service_id", $serviceIds)
            ->groupBy(DB::raw("day(date_created), service_id"))
            ->selectRaw("day(date_created) as day, count(*) as count, service_id")
            ->get();
    }

    public function distinctInquiriesPerMonth(array $serviceIds, $year)
    {
        return MobileUserLog::whereRaw("year(date_created) = ?", [$year])
            ->whereIn("service_id", $serviceIds)
            ->groupBy(DB::raw("month(date_created), service_id"))
            ->selectRaw("month(date_created) as month, count(*) as count, service_id")
            ->get();
    }

    public function distinctInquiriesPerYear(array $serviceIds, $startYear, $endYear)
    {
        return MobileUserLog::whereRaw("year(date_created) >= ?", [$startYear])
            ->whereRaw("year(date_created) <= ?", [$endYear])
            ->whereIn("service_id", $serviceIds)
            ->groupBy(DB::raw("year(date_created), service_id"))
            ->selectRaw("year(date_created) as year, count(*) as count, service_id")
            ->get();
    }

    public function distinctInquiriesDateRange(array $serviceIds, Carbon $dateFrom, Carbon $dateTo, $groupBy = 'day')
    {

        return MobileUserLog::where("date_created", ">=", $dateFrom)
            ->where("date_created", "<=", $dateTo)
            ->whereIn("service_id", $serviceIds)
            ->groupBy(DB::raw("{$groupBy}(date_created), service_id"))
            ->selectRaw("{$groupBy}(date_created) as {$groupBy}, count(*) as count, service_id")
            ->get();
    }

    public function countInquiriesPerService(int $serviceId, Carbon $dateFrom, Carbon $dateTo)
    {
        return MobileUserLog::where('service_id', $serviceId)
            ->where('date_created', '>=', $dateFrom)
            ->where('date_created', '<', $dateTo)
            ->count();
    }

    public function countInquiriesDaily(int $serviceId)
    {
        return MobileUserLog::where('service_id', $serviceId)
            ->where('date_created', '>=', Carbon::today())
            ->where('date_created', '<', Carbon::today()->addDay())
            ->count();
    }

    public function countInquiriesWeekly(int $serviceId)
    {
        return MobileUserLog::where('service_id', $serviceId)
            ->where('date_created', '>=', Carbon::today()->startOfWeek())
            ->where('date_created', '<', Carbon::today()->endOfWeek()->addDay())
            ->count();
    }

    public function countInquiriesMonthly(int $serviceId)
    {
        return MobileUserLog::where('service_id', $serviceId)
            ->where('date_created', '>=', Carbon::today()->startOfMonth())
            ->where('date_created', '<', Carbon::today()->endOfMonth()->addDay())
            ->count();
    }

    public function countInquiriesYearly(int $serviceId, int $year)
    {
        return MobileUserLog::where('service_id', $serviceId)
            ->where('date_created', '>=', new Carbon('first day of ' . $year))
            ->where('date_created', '<', new Carbon('last day of ' . $year))
            ->count();
    }

    public function allLogsInDateRange(Carbon $dateFrom, Carbon $dateTo, int $limit, int $page)
    {
        return MobileUserLog::whereDate('date_created', '>=', $dateFrom)
            ->whereDate('date_created', '<=', $dateTo)
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get();
    }

    public function allLogsInDateRangeCount(Carbon $dateFrom, Carbon $dateTo)
    {
        return MobileUserLog::whereDate('date_created', '>=', $dateFrom)
            ->whereDate('date_created', '<=', $dateTo)
            ->count();
    }

    public function reportDistinctInquiriesPerMonth(array $serviceIds, $year)
    {
        return MobileUserLog::whereRaw("year(tbl_mobile_user_logs.date_created) = ?", [$year])
            ->whereIn("service_id", $serviceIds)
            ->groupBy(DB::raw("month(tbl_mobile_user_logs.date_created), service_id"))
            ->orderBy('month', 'asc')
            ->orderBy('service_id', 'asc')
            ->leftJoin('tbl_services', 'tbl_mobile_user_logs.service_id', '=', 'tbl_services.id')
            ->selectRaw("month(tbl_mobile_user_logs.date_created) as month, count(*) as count, service_id")
            ->get();
    }

    public function reportTotalPerInquiriesPerMonth(array $serviceIds, $year)
    {
        return MobileUserLog::whereRaw("year(tbl_mobile_user_logs.date_created) = ?", [$year])
            ->whereIn("service_id", $serviceIds)
            ->groupBy('service_id')
            ->orderBy('service_id', 'asc')
            ->leftJoin('tbl_services', 'tbl_mobile_user_logs.service_id', '=', 'tbl_services.id')
            ->selectRaw("count(*) as subtotal, service_id")
            ->get();
    }

    public function reportDistinctInquiriesPerDateRange(array $serviceIds, $dateFrom, $dateTo)
    {
        return MobileUserLog::whereDate('tbl_mobile_user_logs.date_created', '>=', $dateFrom)
            ->whereDate('tbl_mobile_user_logs.date_created', '<=', $dateTo)
            ->whereIn("service_id", $serviceIds)
            ->groupBy(DB::raw("month(tbl_mobile_user_logs.date_created), service_id"))
            ->orderBy('month', 'asc')
            ->orderBy('service_id', 'asc')
            ->rightJoin('tbl_services', 'tbl_mobile_user_logs.service_id', '=', 'tbl_services.id')
            ->selectRaw("month(tbl_mobile_user_logs.date_created) as month, count(*) as count, service_id")
            ->get();

    }

    public function reportTotalPerInquiriesPerDateRange(array $serviceIds, $dateFrom, $dateTo)
    {
        return MobileUserLog::whereDate('tbl_mobile_user_logs.date_created', '>=', $dateFrom)
            ->whereDate('tbl_mobile_user_logs.date_created', '<=', $dateTo)
            ->whereIn("service_id", $serviceIds)
            ->groupBy('service_id')
            ->orderBy('service_id', 'asc')
            ->rightJoin('tbl_services', 'tbl_mobile_user_logs.service_id', '=', 'tbl_services.id')
            ->selectRaw("count(*) as subtotal, service_id")
            ->get();
    }

    public function reportDistinctInquiriesPerYear(array $serviceIds, $startYear, $endYear)
    {
        return MobileUserLog::whereRaw("year(tbl_mobile_user_logs.date_created) >= ?", [$startYear])
            ->whereRaw("year(tbl_mobile_user_logs.date_created) <= ?", [$endYear])
            ->whereIn("service_id", $serviceIds)
            ->groupBy(DB::raw("year(tbl_mobile_user_logs.date_created), service_id"))
            ->orderBy('year', 'asc')
            ->orderBy('service_id', 'asc')
            ->rightJoin('tbl_services', 'tbl_mobile_user_logs.service_id', '=', 'tbl_services.id')
            ->selectRaw("year(tbl_mobile_user_logs.date_created) as year, count(*) as count, service_id")
            ->get();
    }

    public function reportTotalPerInquiriesPerYear(array $serviceIds, $startYear, $endYear)
    {

        return MobileUserLog::whereRaw("year(tbl_mobile_user_logs.date_created) >= ?", [$startYear])
            ->whereRaw("year(tbl_mobile_user_logs.date_created) <= ?", [$endYear])
            ->whereIn("service_id", $serviceIds)
            ->groupBy('service_id')
            ->orderBy('service_id', 'asc')
            ->rightJoin('tbl_services', 'tbl_mobile_user_logs.service_id', '=', 'tbl_services.id')
            ->selectRaw("count(*) as subtotal, service_id")
            ->get();
    }

}
