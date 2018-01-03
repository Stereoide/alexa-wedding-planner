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

Route::get('addGuest', 'GuestController@addGuest');
Route::get('removeGuest', 'GuestController@addGuest');
Route::get('', 'GuestController@getGuestStatus');
Route::get('', 'GuestController@getGuestList');
Route::get('', 'GuestController@getUndecidedGuestList');
Route::get('', 'GuestController@getConfirmedGuestList');
Route::get('', 'GuestController@getUnableGuestList');
Route::get('', 'GuestController@confirmGuestStatus');
Route::get('', 'GuestController@calloffGuestStatus');
