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

Route::post('/authorize', 'Auth\ApiTokenController@login');

Route::patch('/token/refresh', 'Auth\ApiTokenController@update');

Route::get('/user', 'Api\User\UserController@authenticated');
