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

//Users
Route::get('/users/all', 'Users\UsersController@all')->name('users.all');
Route::resource('users', 'Users\UsersController')->parameters(['users' => 'user']);
Route::get('/concrete/{roleName}/{action}', 'Users\ConcreteUsersRoutesController');

//Profile
Route::get('profile/{user}', 'ProfilesController@show')->name('profile.show');

//Order
Route::get('/orders/all', 'OrdersController@all')->name('order.all');

Route::get('/orders/{client}/unpaid', 'Orders\OrderPaymentsController@unpaid');

Route::resource('orders', 'OrdersController', ['except' => ['delete', 'edit']]);

Route::get('branch/{branch}/orders', 'OrdersController@filteredByBranch');
Route::get('user/{user}/orders', 'OrdersController@filteredByUser');


//Trips
Route::get('/trips/all', 'Trips\TripsController@all');
Route::resource('trips', 'Trips\TripsController',
    ['except' => ['destroy']]);

Route::get('/trip/{trip}/stored-items/edit', 'Trips\TripStoredItemsController@edit')->name('trip.edit-items');
Route::get('/trip/stored-items/available', 'Trips\TripStoredItemsController@availableItems');
Route::get('/trip/{branch}/stored-items/available', 'Trips\TripStoredItemsController@availableItemsAtBranch');
Route::post('/trip/{trip}/stored-items', 'Trips\TripStoredItemsController@associateToTrip');

//Payments

Route::resource('payments', 'Till\Payments\PaymentsController', ['only' => 'index']);

Route::get('/payments/all', 'Till\Payments\PaymentsController@all')->name('payments.all');
Route::get('/payments/filtered', 'Till\Payments\PaymentsController@filtered');

Route::resource('incoming-payments', 'Till\Payments\IncomingPaymentsController',
    ['except' => ['destroy']]);

Route::resource('outgoing-payments', 'Till\Payments\OutgoingPaymentsController',
    ['only' => ['create', 'store']]);

//StoredItems
Route::get('/stored', 'StoredItemsController@index')->name('stored.index');
Route::get('/stored/all', 'StoredItemsController@all')->name('stored.all');
Route::get('/{branch}/stored', 'StoredItemsController@filteredByBranch');
//Route::post('/stored/trip/{trip}', 'StoredItemsController@associateToTrip');

//Settings
Route::get('/settings', 'AppSettingsController@show');
Route::post('/settings/branch', 'AppSettingsController@storeBranch');
Route::post('/settings/position', 'AppSettingsController@storePosition');

//Tariffs
Route::resource('tariffs', 'TariffsController', [
    'only' => ['index', 'store', 'destroy']
])->parameters(['tariffs' => 'tariff']);

//Tariff price histories
Route::get('/tariff-price-histories/all', "TariffPriceHistoriesController@all");
Route::resource('tariff-price-histories', 'TariffPriceHistoriesController', [
    'only' => ['index', 'create', 'store']
])->parameters(['tariff-price-histories' => 'history']);
Route::get('/tariff-price-histories/{tariff}', "TariffPriceHistoriesController@lastByTariff");

//Cars
Route::get('/cars/all', 'CarsController@all');
Route::resource('cars', 'CarsController')->parameters(['cars' => 'car']);

//Branches
Route::get('/branches/all', "BranchesController@all");
Route::resource('branches', 'BranchesController',
    ['except' => ['create', 'edit', 'show']])->parameters(['branches' => 'branch']);

//PaymentItems
Route::get('/payment-items/all', 'Till\PaymentItemsController@all');
Route::get('/payment-items/type/{type}', 'Till\PaymentItemsController@filteredByType');
Route::resource('payment-items', 'Till\PaymentItemsController',
    ['except' => 'show'])->parameters(['payment-items' => 'paymentItem']);

//Currency
Route::get('/currencies/all', 'Till\CurrenciesController@all')->name('currencies.all');
Route::resource('currencies', 'Till\CurrenciesController',
    ['except' => ['destroy']])->parameters(['currencies' => 'currency']);

//Money Exchange
Route::resource('money-exchanges', 'Till\MoneyExchangesController',
    ['only' => ['create', 'store']])->parameters(['money-exchanges' => 'exchange']);
Route::get('exchange-history/rate/{from}/{to}', 'Till\MoneyExchangesController@exchangeRate');

//Items
Route::get('/items/all', 'ItemsController@all');
Route::get('/items/all/eager', 'ItemsController@allEager');
Route::resource('items', 'ItemsController', ['only' => ['index', 'create', 'store']])->parameters(['items' => 'item']);


Route::get('/search/user/{userInfo}', 'SearchController@findUsersByInfo');

//Countries
Route::get('/countries', 'CountriesController@all');








