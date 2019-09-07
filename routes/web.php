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

//Registration
Route::get('user/create', 'UsersController@create')->middleware('role:employee');
Route::post('user/store', 'UsersController@store')->middleware('role:employee')->name('register-manual');

//Order
Route::get('/order', 'OrdersController@index');
Route::get('/order/create', 'OrdersController@create')->middleware('role:employee');
Route::post('/order/store', 'OrdersController@store')->middleware('role:employee');
Route::patch('/order/update', 'OrdersController@update')->middleware('role:employee');
Route::get('/order/{order}', 'OrdersController@show')->middleware('role:client, employee');

//Settings
Route::get('/settings', 'AppSettingsController@show')->middleware('role:admin');;
Route::post('/settings/branch', 'AppSettingsController@storeBranch')->middleware('role:admin');
Route::post('/settings/position', 'AppSettingsController@storePosition')->middleware('role:admin');

//Tariffs
Route::get('/tariff/create', "TariffsController@create")->middleware('role:admin')->name('create-tariff');
Route::post('/tariff/store', "TariffsController@store")->middleware('role:admin')->name('store-tariff');
Route::delete('/tariff/{tariff}', "TariffsController@delete")->middleware('role:admin');

//Tariff price histories
Route::get('/tariff-price-history/', 'TariffPriceHistoriesController@index')->middleware('role:admin');
Route::get('/tariff-price-history/create', 'TariffPriceHistoriesController@create')->middleware('role:admin');
Route::post('/tariff-price-history/store', 'TariffPriceHistoriesController@store')->middleware('role:admin')->name('store-pricing-history');
Route::get('/tariff-price-history/{tariff}', "TariffPriceHistoriesController@lastByTariff")->middleware('role:employee');

Route::get('/search/user/{userInfo}', 'SearchController@findUsersByInfo')->middleware('role:employee');

Route::get('/items', 'ItemsController@all')->middleware('role:employee');

Route::get('/branches', "BranchesController@all")->middleware('role:employee');;


