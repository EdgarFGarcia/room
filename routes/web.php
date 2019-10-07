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

// Route::get('/', function () {
//     return view('welcome');
// });

//Tagboard
Route::get('/', 'Users\UsersAuthentication@index');
//Reservation
Route::get('/reservation/{id}', 'Api\ReservationController@index');
// turn away
Route::get('/turnaway', 'Api\TurnAwayController@index');
//Event Logs
Route::get('/eventlogs', 'EventLogsController@index');

Route::get('/reports', 'RerpotsControllers@index');

//Autopilot Inspection
Route::get('/historical', 'HistoricalInspectionController@index');


Route::get('test', 'Api\ApiController@testtime');


Route::get('pushnotification', 'Users\UsersAuthentication@push');

Route::get('pushnotificationgroup', 'Users\UsersAuthentication@group');