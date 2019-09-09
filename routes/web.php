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

//Users TODO remove clients and stuff
Route::get('clients', 'UsersController@clients')->middleware('role:admin')->name('user.clients');
Route::get('employees', 'UsersController@employees')->middleware('role:admin')->name('user.employees');
Route::get('user/{role}/only', 'UsersController@filtered')->middleware('role:admin')->name('user.filtered');

//Order
Route::get('/order', 'OrdersController@index')->middleware('role:employee')->name('order.index');
Route::get('/order/create', 'OrdersController@create')->middleware('role:employee')->name('order.create');
Route::post('/order/store', 'OrdersController@store')->middleware('role:employee')->name('order.store');
Route::get('/order/update', 'OrdersController@update')->middleware('role:employee')->name('order.update');
Route::get('/order/all','OrdersController@all')->middleware('role:employee')->name('order.all');
Route::get('/{branch}/orders', 'OrdersController@filteredByBranch')->middleware('role:employee');
Route::get('/order/{order}', 'OrdersController@show')->middleware('role:client, employee')->name('order.show');

//StoredItems
Route::get('/stored','StoredItemsController@index')->middleware('role:employee')->name('stored.index');
Route::get('/stored/all','StoredItemsController@all')->middleware('role:employee')->name('stored.all');
Route::get('/{branch}/stored', 'StoredItemsController@filteredByBranch')->middleware('role:employee');

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


