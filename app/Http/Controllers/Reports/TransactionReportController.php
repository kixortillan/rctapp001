<?php

namespace App\Http\Controllers\Reports;

use App\BL\GraphRanges;
use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Contracts\MobileUserLogsRepositoryInterface;
use App\Repositories\Mobile\Contracts\ServiceTypeRepositoryInterface;
use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Log;
use Validator;

class TransactionReportController extends Controller
{

    protected $mobUserLog;

    protected $services;

    protected $gRangeGen;

    const YEARLY = 'yearly';

    const MONTHLY = 'monthly';

    const DATE_RANGE = 'date_range';

    const YEARLY_HEADERS = ['MONTH', 'RG', 'PN', 'LB', 'LS', 'TC', 'CS', 'FB', 'TOTAL'];

    const MONTHLY_HEADERS = ['MONTH', 'RG', 'PN', 'LB', 'LS', 'TC', 'CS', 'FB', 'TOTAL'];

    const DATE_RANGE_HEADERS = ['MONTH', 'RG', 'PN', 'LB', 'LS', 'TC', 'CS', 'FB', 'TOTAL'];

    public function __construct(MobileUserLogsRepositoryInterface $mobUserLog, ServiceTypeRepositoryInterface $services, GraphRanges $gRangeGen)
    {

        $this->mobUserLog = $mobUserLog;
        $this->services = $services;
        $this->gRangeGen = $gRangeGen;

    }

    public function filters(Request $request)
    {

        $btnMonthsInYear = $this->gRangeGen->monthsInYear(1, 12);
        $btnYears = range(Carbon::today()->year, Carbon::today()->year - 5);

        $response = [
            'modes' => [
                static::YEARLY,
                static::MONTHLY,
                static::DATE_RANGE,
            ],
            'months_in_year' => $btnMonthsInYear,
            'years' => $btnYears,
            'current_year' => Carbon::today()->year,
            'current_month' => Carbon::today()->month,
            'current_month_name' => Carbon::today()->format('F'),
            'default_mode' => static::YEARLY,
        ];

        return response()->json($response);

    }

    public function viewTransactionVolume(Request $request)
    {

        $validator = $this->validateSearchOrDownload($request);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        }

        list($data, $meta) = $this->buildData($request);

        return response()->json([
            'data' => $data,
            'meta' => $meta,
        ]);

    }

    public function download(Request $request, $format = 'xls')
    {

        $validator = $this->validateSearchOrDownload($request);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        }

        list($data, $meta) = $this->buildData($request);

        switch ($format) {

            case 'pdf':
                abort(404, 'Not supported.');
                break;

            case 'xls':
            default:
                return $this->generateXls($data, $meta['headers'], $meta['subtotal_per_month']);
                break;

        }

    }

    public function validateSearchOrDownload(Request $request)
    {

        return Validator::make($request->all(), [
            "mode" => [
                "bail",
                "required",
                "string",
                Rule::in([self::YEARLY, self::MONTHLY, self::DATE_RANGE]),
            ],
            "year" => [
                "bail",
                "required_if:mode,==,yearly",
                "numeric",
            ],
            "date_from" => [
                "bail",
                "required_if:mode,==,date_range",
                "date",
                "before_or_equal:today",
                "same_year:date_to",
            ],
            "date_to" => [
                "bail",
                "required_if:mode,==,date_range",
                "date",
                "before_or_equal:today",
                "after_or_equal:date_from",
                "same_year:date_from",
            ],
            "year_monthly" => [
                "bail",
                "required_if:mode,==,monthly",
                "numeric",
            ],
            "month_from" => [
                "bail",
                "required_if:mode,==,monthly",
                "numeric",
                "min:0",
                "max:11",
            ],
            "month_to" => [
                "bail",
                "required_if:mode,monthly",
                "numeric",
                "greater_than:month_from",
                "min:0",
                "max:11",
            ],
        ]);

    }

    private function buildData(Request $request)
    {
        Log::debug('Parameters:');
        Log::debug(print_r($request->query(), true));

        \DB::enableQueryLog();

        $mode = $request->query('mode', static::YEARLY);

        $serviceIds = [1, 2, 3, 4, 11, 9, 7, 10, 8];

        $data = [];
        $meta = [];

        $serviceTypes = $this->services->findServices($serviceIds);

        switch ($mode) {

            case static::MONTHLY:

                $year = (string) $request->query('year_monthly', Carbon::today()->year);
                $monthFrom = (string) $request->query('month_from', Carbon::today()->month);
                $monthTo = (string) $request->query('month_to', Carbon::today()->month);

                $meta = [
                    'mode' => $mode,
                    'year' => $year,
                    'month_from' => $monthFrom,
                    'month_to' => $monthTo,
                ];

                $dateFrom = Carbon::today()->month($monthFrom + 1)->year($year)->startOfMonth();
                $dateTo = Carbon::today()->month($monthTo + 1)->year($year)->endOfMonth();

                foreach ($serviceTypes as $serviceType) {

                    $data[] = [
                        'code' => $serviceType->code,
                        'logs' => $this->services
                            ->countTransactionInDateRange($serviceType->id,
                                $dateFrom->toDateTimeString(), $dateTo->toDateTimeString()),
                    ];

                }

                $totalPerServices = $this->services
                    ->subTotalPerTransactionsInDateRange($serviceIds,
                        $dateFrom->toDateTimeString(), $dateTo->toDateTimeString());

                $totalPerMonth = $this->services
                    ->subTotalOfTransPerMonthInDateRange($serviceIds,
                        $dateFrom->toDateTimeString(), $dateTo->toDateTimeString());

                $headers = array_map(function ($val) {

                    return [
                        'type' => 'number',
                        'header' => $val,
                    ];

                }, $this->gRangeGen->monthsInYear($dateFrom->month, $dateTo->month));

                // for ($i = ($dateFrom->month - 1); $i < $dateTo->month; $i++) {

                //     $data[] = [
                //         'label' => DateTime::createFromFormat('!m', $i + 1)->format('M'),
                //         'logs' => [],
                //     ];
                // }

                // foreach ($serviceTypes as $key => $id) {

                //     $logs[] = $this->services->countTransactionInDateRange($id,
                //         $dateFrom->toDateString(), $dateTo->toDateString());

                // }

                // array_walk($logs, function ($log) use (&$data) {

                //     array_walk($log, function ($item) use (&$data) {

                //         $data[
                //             array_search(
                //                 DateTime::createFromFormat('!m', $item->month)->format('M'),
                //                 array_column($data, 'label'))
                //         ]['logs'][] = [
                //             'count' => $item->count,
                //             'service_id' => $item->service_id,
                //         ];

                //     });

                // });

                // $totalPerServices = $this->mobUserLog
                //     ->reportTotalPerInquiriesPerDateRange($serviceTypes, $dateFrom->toDateString(), $dateTo->toDateString());

                // $logs = $this->mobUserLog
                //     ->reportDistinctInquiriesPerDateRange($services, $dateFrom, $dateTo);
                // $totalPerServices = $this->mobUserLog
                //     ->reportTotalPerInquiriesPerDateRange($services, $dateFrom, $dateTo);

                // for ($i = 0; $i < Carbon::MONTHS_PER_YEAR; $i++) {

                //     $data[] = array_map(function ($service) use ($i) {

                //         return [
                //             'label' => DateTime::createFromFormat('!m', $i + 1)->format('M'),
                //             'service_id' => $service,
                //             'count' => 0,
                //         ];

                //     }, $services);
                // }

                // foreach ($logs as $log) {

                //     $data[$log->month - 1][
                //         array_search($log->service_id,
                //             array_column($data[$log->month - 1], 'service_id'))]['count'] = $log->count;

                // }

                // $headers = static::MONTHLY_HEADERS;

                break;

            case static::DATE_RANGE:

                $dateFrom = Carbon::parse($request->query('date_from',
                    Carbon::today()->toDateString()));
                $dateTo = Carbon::parse($request->query('date_to',
                    Carbon::today()->toDateString()));

                $dateFrom = $dateFrom->startOfDay();
                $dateTo = $dateTo->endOfDay();

                $meta = [
                    'mode' => $mode,
                    'date_from' => $dateFrom->toDateString(),
                    'date_to' => $dateTo->toDateString(),
                ];

                foreach ($serviceTypes as $serviceType) {

                    $data[] = [
                        'code' => $serviceType->code,
                        'logs' => $this->services
                            ->countTransactionInDateRange($serviceType->id,
                                $dateFrom->toDateTimeString(), $dateTo->toDateTimeString()),
                    ];

                }

                $totalPerServices = $this->services
                    ->subTotalPerTransactionsInDateRange($serviceIds,
                        $dateFrom->toDateTimeString(), $dateTo->toDateTimeString());

                $totalPerMonth = $this->services
                    ->subTotalOfTransPerMonthInDateRange($serviceIds,
                        $dateFrom->toDateTimeString(), $dateTo->toDateTimeString());

                $headers = array_map(function ($val) {

                    return [
                        'type' => 'number',
                        'header' => $val,
                    ];

                }, $this->gRangeGen->monthsInYear($dateFrom->month, $dateTo->month));

                // $logs = [];

                // for ($i = ($dateFrom->month - 1); $i < $dateTo->month; $i++) {

                //     $data[] = [
                //         'label' => DateTime::createFromFormat('!m', $i + 1)->format('M'),
                //         'logs' => [],
                //     ];
                // }

                // foreach ($serviceTypes as $key => $id) {

                //     $logs[] = $this->services->countTransactionInDateRange($id,
                //         $dateFrom->toDateString(), $dateTo->toDateString());

                // }

                // array_walk($logs, function ($log) use (&$data) {

                //     array_walk($log, function ($item) use (&$data) {

                //         $data[
                //             array_search(
                //                 DateTime::createFromFormat('!m', $item->month)->format('M'),
                //                 array_column($data, 'label'))
                //         ]['logs'][] = [
                //             'count' => $item->count,
                //             'service_id' => $item->service_id,
                //         ];

                //     });

                // });

                // $totalPerServices = $this->mobUserLog
                //     ->reportTotalPerInquiriesPerDateRange($serviceTypes, $dateFrom->toDateString(), $dateTo->toDateString());

                // $headers = static::DATE_RANGE_HEADERS;

                break;

            default:
            case static::YEARLY:

                $year = (string) $request->query('year', Carbon::today()->year);

                $dateFrom = Carbon::today()->year($year)->startOfYear();
                $dateTo = Carbon::today()->year($year)->endOfYear();

                $meta = [
                    'mode' => $mode,
                    'year' => $year,
                ];

                //$logs = [];

                // for ($i = 0; $i < Carbon::MONTHS_PER_YEAR; $i++) {

                //     $data[] = [
                //         'label' => DateTime::createFromFormat('!m', $i + 1)->format('M'),
                //         'logs' => [],
                //     ];
                // }

                foreach ($serviceTypes as $serviceType) {

                    $data[] = [
                        'code' => $serviceType->code,
                        'logs' => $this->services
                            ->countTransactionInDateRange($serviceType->id,
                                $dateFrom->toDateTimeString(), $dateTo->toDateTimeString()),
                    ];

                }
                //echo json_encode($logs);die;
                // array_walk($logs, function ($log) use (&$data) {

                //     array_walk($log, function ($item) use (&$data) {

                //         $data[
                //             array_search(
                //                 DateTime::createFromFormat('!m', $item->month)->format('M'),
                //                 array_column($data, 'label'))
                //         ]['logs'][] = [
                //             'count' => $item->count,
                //             'service_id' => $item->service_id,
                //         ];

                //     });

                // });

                $totalPerServices = $this->services
                    ->subTotalPerTransactionsInDateRange($serviceIds,
                        $dateFrom->toDateTimeString(), $dateTo->toDateTimeString());

                $totalPerMonth = $this->services
                    ->subTotalOfTransPerMonthInDateRange($serviceIds,
                        $dateFrom->toDateTimeString(), $dateTo->toDateTimeString());

                // $logs = $this->mobUserLog
                //     ->reportDistinctInquiriesPerMonth($services, $year);
                // $totalPerServices = $this->mobUserLog
                //     ->reportTotalPerInquiriesPerMonth($services, $year);

                // for ($i = 0; $i < Carbon::MONTHS_PER_YEAR; $i++) {

                //     $data[] = array_map(function ($service) use ($i) {

                //         return [
                //             'label' => DateTime::createFromFormat('!m', $i + 1)->format('M'),
                //             'service_id' => $service,
                //             'count' => 0,
                //         ];

                //     }, $services);
                // }

                // foreach ($logs as $log) {

                //     $data[$log->month - 1][
                //         array_search($log->service_id,
                //             array_column($data[$log->month - 1], 'service_id'))]['count'] = $log->count;

                // }

                // foreach ($logs as $log) {

                //     if (!in_array($log->monthName, array_column($data, 'label'))) {

                //         $data[] = [
                //             'label' => $log->monthName,
                //             'logs' => [],
                //         ];

                //     }

                //     $data[$log->month - 1]['logs'][] = [
                //         'count' => $log->count,
                //         'service_id' => $log->service_id,
                //     ];

                // }
                //
                $headers = array_map(function ($val) {

                    return [
                        'type' => 'number',
                        'header' => $val,
                    ];

                }, $this->gRangeGen->monthsInYear(1, 12));

                break;

        }

        Log::debug(\DB::getQueryLog());

        $meta['subtotal_per_service'] = $totalPerServices->toArray();
        $meta['subtotal_per_month'] = $totalPerMonth->toArray();
        $meta['grand_total'] = $totalPerMonth->sum('subtotal');
        $meta['headers'] = array_merge(
            [['type' => 'text', 'header' => 'Service']],
            $headers,
            [['type' => 'number', 'header' => 'Total']]
        );

        return [
            $data,
            $meta,
        ];

    }

    private function generateXls(array $data, array $headers, array $totalPerMonth)
    {

        Excel::create('sss_wa_report', function ($excel) use ($data, $headers, $totalPerMonth) {

            $excel->sheet('report', function ($sheet) use ($data, $headers, $totalPerMonth) {

                $grandTotal = 0;

                //create header
                $sheet->appendRow(array_column($headers, 'header'));
                $sheet->cells('A1:N1', function ($cells) {

                    $cells->setBackground('#757575');

                });

                foreach ($data as $record) {

                    $tempTotal = 0;
                    $tempData = [];
                    $tempData[] = $record['code'];

                    foreach ($record['logs'] as $cellData) {

                        $tempData[] = $cellData->count;

                        $tempTotal += $cellData->count;

                    }

                    $grandTotal += $tempTotal;
                    $tempData[] = $tempTotal;
                    $sheet->appendRow($tempData);

                }

                $sheet->appendRow(array_merge(['Total'],
                    array_column($totalPerMonth, 'subtotal'), [$grandTotal]));

            });

        })->download('xls');

    }

}
