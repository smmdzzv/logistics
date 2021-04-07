<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/authorize', 'Auth\ApiTokenController@login');

Route::patch('/token/refresh', 'Auth\ApiTokenController@update');

Route::get('/user', 'Api\User\UserController@authenticated');

Route::get('/trips', 'Api\Trip\TripsController@activeTrips');
Route::post('/trip/{trip}/unload', 'Api\Trip\TripsController@unloadItem');
Route::post('/trip/{trip}/load', 'Api\Trip\TripsController@loadItem');
Route::post('/trip/{trip}/transfer/{targetTrip}', 'Api\Trip\TripsController@transferItem');

Route::get('/stored-item', 'Api\StoredItem\StoredItemsController@getItem');
