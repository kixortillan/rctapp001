<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Contracts\MobileUserLogsRepositoryInterface;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $mobUserLog;

    public function __construct(MobileUserLogsRepositoryInterface $mobUserLog)
    {
        $this->mobUserLog = $mobUserLog;
    }

    public function viewTransactionVolume(Request $request)
    {

    }

    public function downloadReport(Request $request, $format = 'csv')
    {
        switch ($format) {

            case 'csv':
                return $this->createCsv();
                break;
            default:
                return $this->createXls();
                break;

        }
    }

    private function createXls()
    {

        Excel::create('sss_wa_report', function ($excel) {

            $excel->sheet('report', function ($sheet) {

                //create header
                $sheet->appendRow([
                    'SSS Number',
                    'Service Type',
                    'Loan & Benefit Type',
                    'First Name',
                    'Middle Name',
                    'Last Name',
                    'Date Requested',
                ]);

                $today = Carbon::now();
                $oneMonthBefore = Carbon::now()->subMonth();

                MobileUserLog::with([
                    'user', 'benefitType', 'serviceType',
                ])->whereDate('date_created', '>=', $oneMonthBefore)
                    ->whereDate('date_created', '<=', $today)
                    ->chunk(300, function ($logs) use ($sheet) {

                        foreach ($logs as $item) {
                            $sheet->appendRow([
                                $item->user->sss_no,
                                $item->serviceType->service ?? null,
                                $item->benefitType->type ?? null,
                                $item->user->first_name,
                                $item->user->middle_name,
                                $item->user->last_name,
                                $item->date_created,
                            ]);
                        }

                    });

            });

        })->download('xls');

    }
}
