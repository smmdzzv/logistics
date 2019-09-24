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

Route::resource('users', 'Users\UsersController')->parameters(['users' => 'user'])->middleware('role:admin');

Route::get('/concrete/{roleName}/{action}', 'Users\ConcreteUsersRoutesController');

//Profile
Route::get('profile/{user}', 'ProfilesController@show')->name('profile.show');

//Order
Route::resource('orders', 'OrdersController',
    ['except' => ['delete', 'edit', 'show']])->middleware('role:admin');
Route::get('/orders/{order}', 'OrdersController@show')->middleware('role:client, employee')->name('order.show');

//Trips
Route::get('/trips/{trip}/items/edit', 'TripsController@editStoredList')->middleware('role:manager, director')->name('trip.edit-items');
Route::get('/trips/{trip}/items', 'TripsController@storedItems')->middleware('role:manager, director')->name('trip.items');
Route::get('/trips/all', 'TripsController@all')->middleware('role:manager, director');
Route::resource('trips', 'TripsController',
    ['except' => ['destroy']])->middleware('role:admin');



Route::get('/order/all','OrdersController@all')->middleware('role:employee')->name('order.all');
Route::get('branch/{branch}/orders', 'OrdersController@filteredByBranch')->middleware('role:employee');
Route::get('user/{user}/orders', 'OrdersController@filteredByUser')->middleware('role:employee');


//StoredItems
Route::get('/stored','StoredItemsController@index')->middleware('role:employee')->name('stored.index');
Route::get('/stored/all','StoredItemsController@all')->middleware('role:employee')->name('stored.all');
Route::get('/{branch}/stored', 'StoredItemsController@filteredByBranch')->middleware('role:employee');
Route::post('/stored/trip/{trip}', 'StoredItemsController@associateToTrip')->middleware('role:employee');

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
Route::get('/cars/all', 'CarsController@all')->middleware('role:admin');
Route::resource('cars', 'CarsController')->middleware('role:admin');

//Branches
Route::get('/branches', "BranchesController@all")->middleware('role:employee');
Route::resource('branch', 'BranchesController',
    ['except' => ['create', 'edit', 'show']])->middleware('role:admin');


Route::get('/search/user/{userInfo}', 'SearchController@findUsersByInfo')->middleware('role:employee');

Route::get('/items', 'ItemsController@all')->middleware('role:employee');

Route::get('/countries', 'CountriesController@all')->middleware('role:employee');




