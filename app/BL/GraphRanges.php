<?php

namespace App\BL;

use Carbon\Carbon;

class GraphRanges
{
    public function monthsInYear(int $indexFrom, int $indexTo)
    {
        $months = [
            'January', 'February', 'March', 'April', 'May',
            'June', 'July', 'August', 'September',
            'October', 'November', 'December',
        ];

        return array_slice($months, $indexFrom - 1, ($indexTo - $indexFrom) + 1);
    }

    public function shortMonthsInYear(int $indexFrom, int $indexTo)
    {
        $months = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May',
            'Jun', 'Jul', 'Aug', 'Sep',
            'Oct', 'Nov', 'Dec',
        ];

        return array_slice($months, $indexFrom - 1, ($indexTo - $indexFrom) + 1);
    }

    public function daysInMonth(int $year = null, int $month = null)
    {
        if ($year == null) {
            $year = Carbon::today()->year;
        }

        if ($month == null) {
            $month = Carbon::today()->month;
        }

        $dt = new Carbon();

        $dt->year($year);
        $dt->month($month);

        return range($dt->startOfMonth()->day, $dt->daysInMonth);
    }

    public function wholeDay()
    {
        $arr = range(0, 24);
        return array_map(function ($val) {
            return str_pad($val, 2, 0, STR_PAD_LEFT) . ":00";
        }, $arr);
    }

    public function dayFirstHalf()
    {
        $arr = range(0, 12);
        return array_map(function ($val) {
            return str_pad($val, 2, 0, STR_PAD_LEFT) . ":00";
        }, $arr);
    }

    public function daySecondHalf()
    {
        $arr = range(12, 24);
        return array_map(function ($val) {
            return str_pad($val, 2, 0, STR_PAD_LEFT) . ":00";
        }, $arr);
    }

    public function pastHour()
    {
        $arr = range(0, 60);
        return array_map(function ($val) {
            return str_pad($val, 2, 0, STR_PAD_LEFT) . ":00";
        }, $arr);
    }

    public function daysInWeek()
    {
        return [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday',
            'Thursday', 'Friday', 'Saturday',
        ];
    }
}
