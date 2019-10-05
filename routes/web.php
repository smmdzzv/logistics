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

Route::resource('users', 'Users\UsersController')->parameters(['users' => 'user']);

Route::get('/concrete/{roleName}/{action}', 'Users\ConcreteUsersRoutesController');

//Profile
Route::get('profile/{user}', 'ProfilesController@show')->name('profile.show');

//Order
Route::get('/orders/all', 'OrdersController@all')->name('order.all');
Route::resource('orders', 'OrdersController',
    ['except' => ['delete', 'edit', 'show']]);
Route::get('/orders/{order}', 'OrdersController@show')->name('order.show');

//Trips
Route::get('/trips/{trip}/items/edit', 'TripsController@editStoredList')->name('trip.edit-items');
Route::get('/trips/{trip}/items', 'TripsController@storedItems')->name('trip.items');
Route::get('/trips/all', 'TripsController@all');
Route::resource('trips', 'TripsController',
    ['except' => ['destroy']]);

//Payments
Route::resource('incoming-payments', 'Till\Payments\IncomingPaymentsController',
    ['except' => ['destroy']]);

Route::get('branch/{branch}/orders', 'OrdersController@filteredByBranch');
Route::get('user/{user}/orders', 'OrdersController@filteredByUser');


//StoredItems
Route::get('/stored', 'StoredItemsController@index')->name('stored.index');
Route::get('/stored/all', 'StoredItemsController@all')->name('stored.all');
Route::get('/{branch}/stored', 'StoredItemsController@filteredByBranch');
Route::post('/stored/trip/{trip}', 'StoredItemsController@associateToTrip');

//Settings
Route::get('/settings', 'AppSettingsController@show');
Route::post('/settings/branch', 'AppSettingsController@storeBranch');
Route::post('/settings/position', 'AppSettingsController@storePosition');

//Tariffs
Route::resource('tariffs', 'TariffsController', [
    'only' => ['index', 'store', 'destroy']
])->parameters(['tariffs' => 'tariff']);
//Route::get('/tariff', "TariffsController@index")->name('tariff.index');
//Route::post('/tariff/store', "TariffsController@store")->name('tariff.store');
//Route::delete('/tariff/{tariff}', "TariffsController@delete")->name('tariff.delete');

//Tariff price histories
//Route::get('/tariff-price-history/', 'TariffPriceHistoriesController@index')->name('pricing.index');
//Route::get('/tariff-price-history/create', 'TariffPriceHistoriesController@create')->name('pricing.create');
//Route::post('/tariff-price-history/store', 'TariffPriceHistoriesController@store')->name('pricing.store');
Route::get('/tariff-price-histories/all', "TariffPriceHistoriesController@all");
Route::resource('tariff-price-histories', 'TariffPriceHistoriesController', [
    'only' => ['index', 'create', 'store']
])->parameters(['tariff-price-histories' => 'history']);
Route::get('/tariff-price-histories/{tariff}', "TariffPriceHistoriesController@lastByTariff");

//Cars
Route::get('/cars/all', 'CarsController@all');
Route::resource('cars', 'CarsController');

//Branches
Route::get('/branches/all', "BranchesController@all");
Route::resource('branches', 'BranchesController',
    ['except' => ['create', 'edit', 'show']])->parameters(['branches' => 'branch']);;

//PaymentItems
Route::get('/payment-items/all', 'Till\PaymentItemsController@all');
Route::get('/payment-items/type/{type}', 'Till\PaymentItemsController@filteredByType');
Route::resource('payment-items', 'Till\PaymentItemsController',
    ['except' => 'show'])->parameters(['payment-items' => 'paymentItem']);

//Currency
Route::resource('currencies', 'Till\CurrenciesController',
    ['only' => ['create', 'store']])->parameters(['currencies' => 'currency']);

Route::resource('money-exchanges', 'Till\MoneyExchangesController',
    ['only' => ['create', 'store']])->parameters(['money-exchanges' => 'exchange']);
Route::get('exchange-history/rate/{from}/{to}', 'Till\MoneyExchangesController@exchangeRate');

Route::get('/search/user/{userInfo}', 'SearchController@findUsersByInfo');

Route::get('/items', 'ItemsController@all');

Route::get('/countries', 'CountriesController@all');








