<?php

namespace App\Http\Controllers;

use App\BL\GraphRanges;
use App\Repositories\Mobile\Contracts\LoanBenefitsTypeRepositoryInterface;
use App\Repositories\Mobile\Contracts\MobileUserLogsRepositoryInterface;
use App\Repositories\Mobile\Contracts\ServiceTypeRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;

class DashboardController extends Controller
{
    protected $mobUserLogs;

    protected $serviceTypes;

    protected $loanBenefits;

    protected $gRangeGen;

    protected $modes = [
        'today', 'monthly', 'yearly', 'range',
    ];

    const CONTRIBUTION_STATUS = 1;

    const LOAN_BALANCE_STATUS = 2;

    const LOAN_AND_BENEFIT_STATUS = 4;

    const LOAN_STATUS = 11;

    const SERVICES = [1, 2, 4, 11];

    public function __construct(MobileUserLogsRepositoryInterface $mobUserLogs,
        ServiceTypeRepositoryInterface $serviceTypes,
        LoanBenefitsTypeRepositoryInterface $loanBenefits,
        GraphRanges $gRangeGen) {

        $this->mobUserLogs = $mobUserLogs;
        $this->serviceTypes = $serviceTypes;
        $this->loanBenefits = $loanBenefits;
        $this->gRangeGen = $gRangeGen;

    }

    private function prepareGraphData(string $mode, array $xAxis, array $config)
    {
        $serviceTypes = $this->serviceTypes->findServices(static::SERVICES);

        $data = [];

        switch ($mode) {
            case 'yearly':
                $column = 'month';
                $logs = $this->mobUserLogs->distinctInquiriesPerMonth($serviceTypes->pluck('id')->toArray(), $config['year']);
                break;

            case 'monthly':
                $column = 'day';
                $logs = $this->mobUserLogs->distinctInquiriesPerDay([$service->id], $config['year'], $config['month']);
                break;

            case 'range':
                if ($config['month'] != $config['month2']) {

                    $column = 'month';

                } else {

                    $column = 'day';

                }

                $dateFrom = Carbon::today()->year($config['year'])->month($config['month'])->day($config['day']);
                $dateTo = Carbon::today()->year($config['year'])->month($config['month2'])->day($config['day2']);
                $logs = $this->mobUserLogs->distinctInquiriesDateRange([$service->id], $dateFrom, $dateTo, $column);

                break;

            default:
            case 'today':
                $column = 'hour';
                $logs = $this->mobUserLogs
                    ->distinctInquiriesPerHour(
                        $serviceTypes->pluck('id')->toArray(),
                        $config['year'], $config['month'], $config['day']);
                break;
        }

        Log::debug($logs);

        $current = null;
        $currentData = [];

        foreach ($logs as $log) {

            if ($current != $log->service_id
                && $current != null) {
                $data[] = $currentData;

                //clear data
                $currentData = [];

                //
                $current = $log->service_id;

                $data[] = [
                    'month' => $log->month,
                    $serviceTypes->where('id', $log->service_id)->get('name') => $log->count,
                ];

                continue;
            }

        }

        // foreach ($serviceTypes as $service) {

        // switch ($mode) {
        //     case 'yearly':
        //         $column = 'month';
        //         $logs = $this->mobUserLogs->distinctInquiriesPerMonth([$service->id], $config['year']);
        //         break;

        //     case 'monthly':
        //         $column = 'day';
        //         $logs = $this->mobUserLogs->distinctInquiriesPerDay([$service->id], $config['year'], $config['month']);
        //         break;

        //     case 'range':
        //         // $column = 'day';
        //         // $dateFrom = Carbon::today()->year($config['year'])->month($config['month'])->day($config['day']);
        //         // $dateTo = Carbon::today()->year($config['year'])->month($config['month'])->day($config['day'])->addWeek();
        //         // $logs = $this->mobUserLogs->distinctInquiriesDateRange([$service->id], $dateFrom, $dateTo);

        //         if ($config['month'] != $config['month2']) {

        //             $column = 'month';

        //         } else {

        //             $column = 'day';

        //         }

        //         $dateFrom = Carbon::today()->year($config['year'])->month($config['month'])->day($config['day']);
        //         $dateTo = Carbon::today()->year($config['year'])->month($config['month2'])->day($config['day2']);
        //         $logs = $this->mobUserLogs->distinctInquiriesDateRange([$service->id], $dateFrom, $dateTo, $column);

        //         break;

        //     default:
        //     case 'today':
        //         $column = 'hour';
        //         $logs = $this->mobUserLogs->distinctInquiriesPerHour([$service->id], $config['year'], $config['month'], $config['day']);
        //         break;
        // }

        // $plot = [];
        // $initData = array_fill(0, count($xAxis), 0);
        // $color = $this->getLegendColor($service->id);
        // $plot = [
        //     'label' => $service->service,
        //     //'fillColor' => 'rgba(255,255,255, 0)',
        //     //'borderColor' => $color,
        //     //'pointBackgroundColor' => $color,
        //     'id' => $service->id,
        //     'color' => $color,
        //     'data' => $initData,
        // ];

        // foreach ($logs as $record) {

        //     switch ($mode) {
        //         case 'yearly':
        //             $index = array_search(
        //                 Carbon::today()->month($record->{$column})->format('F'), $xAxis);
        //             break;

        //         case 'monthly':
        //             $index = array_search($record->{$column}, $xAxis);
        //             break;

        //         case 'range':
        //             if ($column == 'month') {
        //                 $index = array_search(Carbon::today()->month($record->{$column})->format('F'), $xAxis);
        //             } else {
        //                 $index = array_search($record->{$column}, $xAxis);
        //             }
        //             break;

        //         default:
        //         case 'today':
        //             $index = array_search($record->{$column}, $xAxis);
        //             break;
        //     }

        //     $plot['data'][$index] = $record->count;

        // }

        //     $data[] = $plot;
        // }

        return $data;
    }

    private function getLegendColor($service)
    {
        switch ($service) {
            case static::LOAN_STATUS:
                return '#4CAF50';

            case static::LOAN_BALANCE_STATUS:
                return '#FFC107';

            case static::LOAN_AND_BENEFIT_STATUS:
                return '#00BCD4';

            default:
            case static::CONTRIBUTION_STATUS:
                return '#F44336';
        }
    }

    public function generateDataForKnobs(Request $request)
    {
        Log::debug('Parameters:');
        Log::debug(print_r($request->query(), true));

        //modes for dashboard based on query string parameters
        $mode = $request->query('mode', 'today');
        $dailyDuration = $request->query('daily_duration', '24');
        $month = $request->query('month', Carbon::today()->month);
        $month2 = $request->query('month2', Carbon::today()->month);
        $year = $request->query('year', Carbon::today()->year);
        $day = $request->query('day', Carbon::today()->day);
        $day2 = $request->query('day2', Carbon::today()->day);

        $serviceTypes = $this->serviceTypes->findServices(static::SERVICES);

        $response = [];
        $response['knobs'] = [];

        foreach ($serviceTypes as $item) {
            $temp['id'] = $item->id;
            $temp['text'] = $item->service;

            $count = $this->countForKnob($item->id, $mode, [
                'daily_duration' => $dailyDuration,
                'month' => $month,
                'month2' => $month2,
                'year' => $year,
                'day' => $day,
                'day2' => $day2,
            ]);
            $temp['percent'] = ($count > 1000) ? 100 : ($count / 1000) * 100;

            $temp['color'] = $this->getLegendColor($item->id);

            $response['knobs'][] = $temp;
        }

        list($startDate, $endDate) = $this->createDateRange([
            'mode' => $mode,
            'month' => $month,
            'month2' => $month2,
            'day' => $day,
            'day2' => $day2,
            'year' => $year,
        ]);

        $response['loans_and_benefits_application']
        = $this->loanBenefits->countLoansAndBenefitsApplication($startDate->toDateTimeString(),
            $endDate->toDateTimeString());

        $response['mode'] = $mode;
        $response['year'] = $year;
        $response['month'] = $month;
        $response['day'] = $day;

        if ($month2 && $day2) {
            $response['month2'] = $month2;
            $response['day2'] = $day2;
        }

        return response()->json($response);
    }

    private function createDateRange(array $config)
    {
        $dateFrom = new Carbon();
        $dateTo = new Carbon();

        switch ($config['mode']) {
            case 'yearly':
                $dateFrom->startOfYear();
                $dateTo->endOfYear();
                break;

            case 'monthly':
                $dateFrom->year($config['year'])->month($config['month'])->startOfMonth();
                $dateTo->year($config['year'])->month($config['month'])->endOfMonth();
                break;

            case 'range':
                $dateFrom->year($config['year'])->month($config['month'])->day($config['day']);
                $dateTo->year($config['year'])->month($config['month2'])->day($config['day2']);
                break;

            default:
            case 'today':
                $dateFrom->today()->startOfDay();
                $dateTo->today()->endOfDay();
                break;
        }

        return [$dateFrom, $dateTo];
    }

    private function countForKnob(int $serviceId, string $mode, array $config)
    {
        if ($mode == 'today') {

            if ($config['daily_duration'] == '12') {

                if (Carbon::now()->hour > 11) {
                    //0 - 12
                    $dateFrom = Carbon::today();
                    $dateTo = Carbon::today()->hour(12);
                } else {
                    //12 - 24
                    $dateFrom = Carbon::today()->hour(12);
                    $dateTo = Carbon::today()->addDay();
                }

            } else {
                //default is 24 hours
                $dateFrom = Carbon::today();
                $dateTo = Carbon::today()->addDay()->endOfDay();
            }

            return $this->mobUserLogs->countInquiriesPerService($serviceId, $dateFrom, $dateTo);

        }

        if ($mode == 'range') {
            $dateFrom = Carbon::today()->year($config['year'])->month($config['month'])->day($config['day']);
            //$dateTo = Carbon::today()->year($config['year'])->month($config['month'])->day($config['day'])->addWeek();
            $dateTo = Carbon::today()->year($config['year'])->month($config['month2'])->day($config['day2'])->endOfDay();

            return $this->mobUserLogs->countInquiriesPerService($serviceId, $dateFrom, $dateTo);
        }

        if ($mode == 'monthly') {
            $dateFrom = Carbon::today()->month($config['month'])->startOfMonth()->year($config['year']);
            $dateTo = Carbon::today()->month($config['month'])->endOfMonth()->year($config['year'])->endOfDay();

            return $this->mobUserLogs->countInquiriesPerService($serviceId, $dateFrom, $dateTo);
        }

        if ($mode == 'yearly') {
            $dateFrom = Carbon::today()->startOfYear()->year($config['year']);
            $dateTo = Carbon::today()->endOfYear()->year($config['year'])->endOfDay();

            return $this->mobUserLogs->countInquiriesPerService($serviceId, $dateFrom, $dateTo);
        }
    }

    public function generateDataForLineGraph(Request $request)
    {
        Log::debug('Parameters:');
        Log::debug(print_r($request->query(), true));

        //modes for dashboard based on query string parameters
        $mode = $request->query('mode', 'today');
        $dailyDuration = $request->query('daily_duration', '24');
        $month = $request->query('month', Carbon::today()->month);
        $month2 = $request->query('month2', Carbon::today()->month);
        $year = $request->query('year', Carbon::today()->year);
        $day = $request->query('day', Carbon::today()->day);
        $day2 = $request->query('day2', Carbon::today()->day);

        $xLine = $this->prepareX($mode, [
            'daily_duration' => $dailyDuration,
            'month' => $month,
            'month2' => $month2,
            'year' => $year,
            'day' => $day,
            'day2' => $day2,
        ]);

        $yLine = $this->prepareGraphData($mode, $xLine, [
            'daily_duration' => $dailyDuration,
            'month' => $month,
            'month2' => $month2,
            'year' => $year,
            'day' => $day,
            'day2' => $day2,
        ]);

        $response = [
            'x_axis' => $xLine,
            'y_axis' => $yLine,
        ];

        $response['mode'] = $mode;
        $response['year'] = $year;
        $response['month'] = $month;
        $response['day'] = $day;

        return response()->json($response);
    }

    private function prepareX($mode, array $config)
    {
        $range = new GraphRanges;

        if ($mode == 'today' && $config['daily_duration'] == '24') {
            return $range->wholeDay();
        }

        if ($mode == 'today' && $config['daily_duration'] == '12') {
            //check current time if before 12nn or past 12nn
            if (Carbon::now()->hour < 12) {
                return $range->dayFirstHalf();
            }

            return $range->daySecondHalf();
        }

        if ($mode == 'today' && $config['daily_duration'] == '1') {
            return $range->pastHour();
        }

        if ($mode == 'range') {

            if ($config['month'] != $config['month2']) {

                //return range($config['month'], $config['month2']);
                return $range->shortMonthsInYear($config['month'], $config['month2']);

            } else {

                return range($config['day'], $config['day2']);

            }

            // return range($config['day'], $config['day2']);
        }

        if ($mode == 'monthly') {
            return $range->daysInMonth($config['year'], $config['month']);
        }

        if ($mode == 'yearly') {
            return $range->shortMonthsInYear(1, 12);
        }
    }

    public function graphDataForSevices(Request $request)
    {
        Log::debug('Parameters: ');
        Log::debug(print_r($request->all(), true));

        $serviceIds = explode(',', $request->query('svcs', ''));
        $mode = $request->query('mode', 'today');

        $dateToday = Carbon::today();

        switch ($mode) {

            case 'yearly':
                $year = $request->query('year', $dateToday->year);
                $dateTime = Carbon::today()->year($year);
                $dateFrom = $dateTime->startOfYear()->toDateTimeString();
                $dateTo = $dateTime->endOfYear()->toDateTimeString();
                break;

            case 'monthly':
                $year = $request->query('year', $dateToday->year);
                $month = $request->query('month', $dateToday->month);
                $dateTime = Carbon::today()->year($year)->month($month);
                $dateFrom = $dateTime->startOfMonth()->toDateTimeString();
                $dateTo = $dateTime->endOfMonth()->toDateTimeString();
                break;

            case 'date_range':
                $dateFrom = Carbon::parse($request->query('from',
                    $dateToday->startOfDay()))->toDateTimeString();
                $dateTo = Carbon::parse($request->query('to',
                    $dateToday->endOfDay()))->toDateTimeString();
                break;

            default:
                $dateFrom = $dateToday->startOfDay()->toDateTimeString();
                $dateTo = $dateToday->endOfDay()->toDateTimeString();
                break;

        }

        $response = [
            'meta' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ];

        return response()->json($response);
    }

    public function validategraphDataForSevices(Request $request)
    {
        return Validator::make($request->query(), [
            'mode' => [
                'bail',
                'required',
            ],
        ]);
    }

    public function filters(Request $request)
    {

        $month = $request->query('month', Carbon::today()->month);
        $year = $request->query('year', Carbon::today()->year);

        $btnHours = ['24', '12', '1'];
        $btnMonthsInYear = $this->gRangeGen->monthsInYear(1, 12);
        $btnYears = range(Carbon::today()->year, Carbon::today()->year - 5);
        $btnModes = $this->modes;
        $btnDays = $this->gRangeGen->daysInMonth($year, $month);

        $response = [
            'modes' => $btnModes,
            'hours' => $btnHours,
            'days' => $btnDays,
            'months_in_year' => $btnMonthsInYear,
            'years' => $btnYears,
            'current_year' => Carbon::today()->year,
            'current_month' => Carbon::today()->format('F'),
            'current_day' => Carbon::today()->day,
            'default_mode' => 'today',
        ];

        return response()->json($response);
    }

}
