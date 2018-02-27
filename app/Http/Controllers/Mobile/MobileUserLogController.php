<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Contracts\MobileUserLogsRepositoryInterface;
use App\Transformers\Mobile\MobileUserLogTransformer;
use App\Utilities\FractalResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MobileUserLogController extends Controller
{
    private $mobUserLog;

    public function __construct(MobileUserLogsRepositoryInterface $mobUserLog)
    {
        $this->mobUserLog = $mobUserLog;
    }

    public function view(Request $request)
    {
        $dateFrom = $request->query('from');
        $dateTo = $request->query('to');
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);

        $dateFrom = Carbon::parse($dateFrom);
        $dateTo = Carbon::parse($dateTo);

        $logs = $this->mobUserLog->allLogsInDateRange($dateFrom, $dateTo, $perPage, $page);
        $totalLogs = $this->mobUserLog->allLogsInDateRangeCount($dateFrom, $dateTo);

        $paginator = new LengthAwarePaginator($logs, $totalLogs, $perPage, $page);
        $paginator->appends($request->query());

        return response()->json((new FractalResponse($paginator, MobileUserLogTransformer::class))->output());
    }
}
