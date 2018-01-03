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

Route::POST('/addGuest', 'GuestController@addGuest');
Route::POST('/removeGuest', 'GuestController@addGuest');
Route::POST('/getGuestsStatus', 'GuestController@getGuestsStatus');
Route::POST('/getGuestsList', 'GuestController@getGuestsList');
Route::POST('/getUndecidedGuestsList', 'GuestController@getUndecidedGuestsList');
Route::POST('/getConfirmedGuestsList', 'GuestController@getConfirmedGuestsList');
Route::POST('/getUnableGuestsList', 'GuestController@getUnableGuestsList');
Route::POST('/confirmGuestStatus', 'GuestController@confirmGuestStatus');
Route::POST('/calloffGuestStatus', 'GuestController@calloffGuestStatus');
