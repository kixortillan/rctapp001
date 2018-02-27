<?php

namespace App\Http\Controllers\Auth;

use App\BL\Auth\Authenticate;
use App\BL\GraphRanges;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use GuzzleHttp;
use Illuminate\Http\Request;
use Validator;

class LoginController extends Controller
{
    protected $gRangeGen;

    public function __construct(GraphRanges $gRangeGen)
    {
        $this->gRangeGen = $gRangeGen;
    }

    public function login(Request $request)
    {
        $validator = $this->validateLogin($request);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        }

        try {

            $response = Authenticate::login($request->input('username'),
                $request->input('password'));

        } catch (GuzzleHttp\Exception\ClientException $ex) {

            $response = $ex->getResponse();
            $body = json_decode((string) $response->getBody(), true);

            return response()->json([
                'error' => $body['message'],
            ], 422);

        }

        $response = array_merge($response, ['filters' => $this->initFilters()]);

        return response()->json($response);

        // $http = new GuzzleHttp\Client([
        //     'base_uri' => env('APP_URL'),
        // ]);

        // try {

        //     $response = $http->post('/oauth/token', [
        //         'form_params' => [
        //             'grant_type' => 'password',
        //             'client_id' => env('OAUTH2_CLIENT_ID'),
        //             'client_secret' => env('OAUTH2_CLIENT_SECRET'),
        //             'username' => $request->input('username'),
        //             'password' => $request->input('password'),
        //             'scope' => '',
        //         ],
        //     ]);

        // } catch (GuzzleHttp\Exception\ClientException $ex) {

        //     $response = $ex->getResponse();
        //     $body = json_decode((string) $response->getBody(), true);

        //     return response()->json([
        //         'error' => $body['message'],
        //     ], 422);

        // }

        // $body = json_decode((string) $response->getBody(), true);

        // try {

        //     $response = $http->get('/api/user', [
        //         'headers' => [
        //             'Authorization' => 'Bearer ' . $body['access_token'],
        //         ],
        //     ]);

        // } catch (GuzzleHttp\Exception\ClientException $ex) {

        //     $response = $ex->getResponse();
        //     $body = json_decode((string) $response->getBody(), true);

        //     return response()->json([
        //         'error' => $body['message'],
        //     ], 422);

        // }

        // $userInfo = json_decode((string) $response->getBody(), true);

        // $user = $userInfo['data']['user']['data'];
        // $roles = $userInfo['data']['roles']['data'];

        // $response = array_merge($body, [
        //     'user' => $user,
        //     'roles' => $roles,
        //     'filters' => $this->initFilters(),
        // ]);

        // return response()->json($response);
    }

    public function validateLogin(Request $request)
    {
        return Validator::make($request->input(), [
            'username' => [
                'bail',
                'required',
                'max:150',

            ],

            'password' => [
                'bail',
                'required',
                'min:8',
                'max:150',
            ],
        ]);
    }

    private function initFilters()
    {
        $month = Carbon::today()->month;
        $year = Carbon::today()->year;
        $day = Carbon::today()->day;
        $btnHours = ['24', '12', '1'];
        $btnMonthsInYear = $this->gRangeGen->monthsInYear(1, 12);
        $btnYears = range($year, $year - 5);
        $btnModes = ['today', 'monthly', 'yearly', 'range'];
        $btnDays = $this->gRangeGen->daysInMonth($year, $month);

        return [

            'reports' => [

                'modes' => [
                    'yearly',
                    'monthly',
                    'date_range',
                ],
                'default_mode' => 'yearly',

            ],

            'dashboard' => [

                'modes' => [
                    'today',
                    'monthly',
                    'yearly',
                    'range',
                ],
                'default_mode' => 'today',

            ],

            'hours' => $btnHours,
            'days' => $btnDays,
            'months_in_year' => $btnMonthsInYear,
            'months_in_year' => $this->gRangeGen->monthsInYear(1, 12),
            'short_months_in_year' => $this->gRangeGen->shortMonthsInYear(1, 12),
            'years' => $btnYears,
            'current_year' => $year,
            'current_month' => Carbon::today()->month,
            'current_short_month_name' => Carbon::today()->format('M'),
            'current_month_name' => Carbon::today()->format('F'),
            'current_day' => $day,
        ];
    }
}
