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
Route::get('clients', 'UsersController@clients')->middleware('role:admin')->name('user.clients');
Route::get('employees', 'UsersController@employees')->middleware('role:admin')->name('user.employees');
Route::get('user/{user}/edit', 'UsersController@edit')->middleware('role:employee')->name('user.edit');
Route::get('user/{role}/only', 'UsersController@filtered')->middleware('role:admin')->name('user.filtered');
Route::patch('user/{user}', 'UsersController@update')->middleware('role:employee')->name('user.update');

//Profile
Route::get('profile/{user}', 'ProfilesController@show')->name('profile.show');

//Order
//Route::get('/order', 'OrdersController@index')->middleware('role:employee')->name('order.index');
//Route::get('/order/create', 'OrdersController@create')->middleware('role:employee')->name('order.create');
//Route::post('/order/store', 'OrdersController@store')->middleware('role:employee')->name('order.store');
//Route::get('/order/update', 'OrdersController@update')->middleware('role:employee')->name('order.update');

Route::resource('orders', 'OrdersController',
    ['except' => ['delete', 'edit', 'show']])->middleware('role:admin');
Route::get('/orders/{order}', 'OrdersController@show')->middleware('role:client, employee')->name('order.show');

Route::resource('trip', 'TripsController',
    ['only' => ['create', 'store']])->middleware('role:admin');


Route::get('/order/all','OrdersController@all')->middleware('role:employee')->name('order.all');
Route::get('branch/{branch}/orders', 'OrdersController@filteredByBranch')->middleware('role:employee');
Route::get('user/{user}/orders', 'OrdersController@filteredByUser')->middleware('role:employee');


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

//Cars
Route::get('/car', 'CarsController@index')->middleware('role:employee')->name('car.index');
Route::get('/car/create', 'CarsController@create')->middleware('role:employee')->name('car.create');
Route::get('/car/{car}', 'CarsController@show')->middleware('role:employee')->name('car.show');
Route::post('/car', 'CarsController@store')->middleware('role:employee')->name('car.store');
Route::get('/car/{car}/edit', 'CarsController@edit')->middleware('role:employee')->name('car.edit');
Route::patch('/car/{car}', 'CarsController@update')->middleware('role:employee')->name('car.update');
Route::delete('/car/{car}', 'CarsController@destroy')->middleware('role:employee')->name('car.delete');

//Branches
Route::get('/branches', "BranchesController@all")->middleware('role:employee');

//Route::get('/branch', 'BranchesController@index')->middleware('role:employee')->name('branch.index');
//Route::get('/branch/create', 'BranchesController@create')->middleware('role:employee')->name('branch.create');
//Route::get('/branch/{branch}', 'BranchesController@show')->middleware('role:employee')->name('branch.show');
//Route::post('/branch', 'BranchesController@store')->middleware('role:employee')->name('branch.store');
//Route::get('/branch/{branch}/edit', 'BranchesController@edit')->middleware('role:employee')->name('branch.edit');
//Route::patch('/branch/{branch}', 'BranchesController@update')->middleware('role:employee')->name('branch.update');
//Route::delete('/branch/{branch}', 'BranchesController@destroy')->middleware('role:employee')->name('branch.delete');
Route::resource('branch', 'BranchesController',
    ['except' => ['create', 'edit', 'show']])->middleware('role:admin');


Route::get('/search/user/{userInfo}', 'SearchController@findUsersByInfo')->middleware('role:employee');

Route::get('/items', 'ItemsController@all')->middleware('role:employee');

Route::get('/countries', 'CountriesController@all')->middleware('role:employee');




