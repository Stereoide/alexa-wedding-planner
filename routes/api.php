<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/addGuest', 'GuestController@addGuest');
Route::get('/removeGuest', 'GuestController@addGuest');
Route::get('/getGuestsStatus', 'GuestController@getGuestsStatus');
Route::get('/getGuestsList', 'GuestController@getGuestsList');
Route::get('/getUndecidedGuestsList', 'GuestController@getUndecidedGuestsList');
Route::get('/getConfirmedGuestsList', 'GuestController@getConfirmedGuestsList');
Route::get('/getUnableGuestsList', 'GuestController@getUnableGuestsList');
Route::get('/confirmGuestStatus', 'GuestController@confirmGuestStatus');
Route::get('/calloffGuestStatus', 'GuestController@calloffGuestStatus');
