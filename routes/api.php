<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

/*
|------------------------------------------------------------------------------------------------
| Authentication API's
|
|------------------------------------------------------------------------------------------------
|
 */
Route::post('/login', 'Auth\LoginController@login');

/*
|------------------------------------------------------------------------------------------------
| Credential Retrieval API's
|
|------------------------------------------------------------------------------------------------
|
 */
Route::post('/forgot/password', 'Auth\ForgotPasswordController@forgotPassword');
Route::post('/change/password/{token}', 'Auth\ChangePasswordController@change');

/*
|------------------------------------------------------------------------------------------------
| Protected API's
|------------------------------------------------------------------------------------------------
| Below API's are protected by OAuth 2.0
| Therefore, it is mandatory to attach needed credentials
| following Laravel Passport API to access them.
|
 */
Route::middleware(['auth:api', 'enable_log', 'disable_log'])->group(function () {

    /*
    |------------------------------------------------------------------------------------------------
    | Registration API's
    |------------------------------------------------------------------------------------------------
    |
     */
    Route::post('/users/register', 'Auth\RegisterController@createAccount')
        ->name('user.register');

    /*
    |------------------------------------------------------------------------------------------------
    | Logged in User API's
    |------------------------------------------------------------------------------------------------
    |
     */
    Route::get('/user', 'User\AccountController@user')
        ->name('user.info');

    /*
    |------------------------------------------------------------------------------------------------
    | User Account API's
    |------------------------------------------------------------------------------------------------
    |
     */
    Route::get('/users/employees', 'User\AccountController@employeesOnly')
        ->name('view.user.employees');
    Route::get('/users/admins', 'User\AccountController@adminsOnly')
        ->name('view.user.admins');
    Route::get('/users/user/{username}', 'User\AccountController@viewAccountInfo')
        ->name('user.register');

    /*
    |---------------------------------------------------------------------------------
    | Graph API's
    |---------------------------------------------------------------------------------
    |
     */
    Route::get('/graphs/services/line', 'DashboardController@graphDataForSevices')
        ->name('graph.services.line');

    /*
    |------------------------------------------------------------------------------------------------
    | Dashboard API's
    |------------------------------------------------------------------------------------------------
    |
     */
    Route::get('/dashboard/stats/knobs', 'DashboardController@generateDataForKnobs')
        ->name('dashboard.knobs.data');

    Route::get('/dashboard/stats/linegraph', 'DashboardController@generateDataForLineGraph')
        ->name('dashboard.line.data');

    Route::get('/dashboard/stats/filters', 'DashboardController@filters')
        ->name('dashboard.filters');

    /*
    |------------------------------------------------------------------------------------------------
    | Reports API's
    |------------------------------------------------------------------------------------------------
    |
     */
    Route::get('/reports/transactions', 'Reports\TransactionReportController@viewTransactionVolume')
        ->name('view.reports.transactions');

    Route::get('/reports/transactions/download/{format?}', 'Reports\TransactionReportController@download')
        ->name('download.reports.transactions');

    Route::get('/reports/transactions/filters', 'Reports\TransactionReportController@filters')
        ->name('view.reports.transactions.filters');

    /*
    |------------------------------------------------------------------------------------------------
    |
    |------------------------------------------------------------------------------------------------
    |
     */
    Route::get('/mobile/users/logs', 'Mobile\MobileUserLogController@view')
        ->name('view.mobile.userlogs');

});

// Route::post('/abizo/users/user', function (\Illuminate\Http\Request $request) {

//     \Log::debug('Parameters:');
//     \Log::debug($request->all());

//     return response()->json();
// });
