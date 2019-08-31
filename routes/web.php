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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/order/create', 'OrdersController@create')->name('order.create');

Route::get('/settings', 'AppSettingsController@show')->name('settings.show');
Route::post('/settings/branch', 'AppSettingsController@storeBranch');
Route::post('/settings/position', 'AppSettingsController@storePosition');

Route::get('/search/user/{userInfo}', 'SearchController@findUsersByInfo');

Route::get('/items', 'ItemsController@all');
Route::get('/item/validator', 'ItemsController@validator');

Route::get('/branches', "BranchesController@all");
