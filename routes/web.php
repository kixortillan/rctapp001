<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/abizo/wifi/login', function (Request $request) {

    return view('welcome');

});

Route::get('/register/verify', 'Auth\RegisterController@verifyRegistration')
    ->name('user.register.verify');

Route::get('/{path?}', function () {
    return view('layouts.app');
})->where(['path' => '.*']);

// Auth::routes();

// Route::get('', 'Auth\LoginController@showLoginForm');

// Route::get('register/verify', 'Auth\RegisterController@verifyRegistration');

// Route::get('dashboard/statistics/knobs', 'DashboardController@generateDataForKnobs')
//     ->name('dashboard.knobs.data');

// Route::get('dashboard/statistics/linegraph', 'DashboardController@generateDataForLineGraph')
//     ->name('dashboard.line.data');

// Route::middleware('auth')->group(function () {

//     Route::get('dashboard', 'DashboardController@index')
//         ->name('m.dashboard');

//     Route::get('dashboard/report/{format?}', 'DashboardController@downloadReport')
//         ->name('dashboard.dl');

//     Route::get('users/employees', 'User\AccountController@employeesOnly')
//         ->name('m.users.employees');

//     Route::get('users/admin', 'User\AccountController@adminsOnly')
//         ->name('m.users.admin');

//     Route::get('users/profile/{username}', 'User\AccountController@viewAccountInfo')
//         ->name('view.acct.info');

//     Route::post('users/acl/{id}', 'User\AccountController@updateAssignedRoles')
//         ->name('update.acct.acl');

//     Route::post('users/profile/photo', 'User\AccountController@updateProfilePic')
//         ->name('update.acct.pic');

//     Route::get('users/security/password', 'User\AccountController@updatePasswordShowForm')
//         ->name('view.update.password');

//     Route::post('users/security/password', 'User\AccountController@updatePassword')
//         ->name('update.password');

//     Route::get('reports', 'User\ReportController@view')
//         ->name('m.reports');
// });
