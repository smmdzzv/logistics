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
Route::get('user/create', 'UsersController@create')->middleware('role:employee')->name('user.create');
Route::post('user/store', 'UsersController@store')->middleware('role:employee')->name('user.store');
Route::get('user/{user}/edit', 'UsersController@edit')->middleware('role:employee')->name('user.edit');
Route::patch('user/{user}', 'UsersController@update')->middleware('role:employee')->name('user.update');

//Users
Route::get('clients', 'UsersController@clients')->middleware('role:admin')->name('user.clients');
Route::get('stuff', 'UsersController@stuff')->middleware('role:admin')->name('user.stuff');

//Order
Route::get('/order', 'OrdersController@index')->middleware('role:employee')->name('order.index');
Route::get('/order/create', 'OrdersController@create')->middleware('role:employee')->name('order.create');
Route::post('/order/store', 'OrdersController@store')->middleware('role:employee')->name('order.store');
Route::patch('/order/update', 'OrdersController@update')->middleware('role:employee')->name('order.update');
Route::get('/order/{order}', 'OrdersController@show')->middleware('role:client, employee')->name('order.show');

//Settings
Route::get('/settings', 'AppSettingsController@show')->middleware('role:admin');
Route::post('/settings/branch', 'AppSettingsController@storeBranch')->middleware('role:admin');
Route::post('/settings/position', 'AppSettingsController@storePosition')->middleware('role:admin');

//Tariffs
Route::get('/tariff/create', "TariffsController@create")->middleware('role:admin')->name('tariff.create');
Route::post('/tariff/store', "TariffsController@store")->middleware('role:admin')->name('tariff.store');
Route::delete('/tariff/{tariff}', "TariffsController@delete")->middleware('role:admin')->name('tariff.delete');

//Tariff price histories
Route::get('/tariff-price-history/', 'TariffPriceHistoriesController@index')->middleware('role:admin')->name('pricing.index');
Route::get('/tariff-price-history/create', 'TariffPriceHistoriesController@create')->middleware('role:admin')->name('pricing.create');
Route::post('/tariff-price-history/store', 'TariffPriceHistoriesController@store')->middleware('role:admin')->name('pricing.store');
Route::get('/tariff-price-history/{tariff}', "TariffPriceHistoriesController@lastByTariff")->middleware('role:employee');

Route::get('/search/user/{userInfo}', 'SearchController@findUsersByInfo')->middleware('role:employee');

Route::get('/items', 'ItemsController@all')->middleware('role:employee');

Route::get('/branches', "BranchesController@all")->middleware('role:employee');;


