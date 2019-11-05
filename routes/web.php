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
Route::get('/user/find', 'Users\UsersController@find');

//Profile
Route::get('profile/{user}', 'ProfilesController@show')->name('profile.show');

//Order
Route::get('/orders/all', 'OrdersController@all')->name('order.all');
Route::get('/orders/{client}/active', 'OrdersController@activeOrders');

Route::get('/orders/items/edit', 'Orders\OrderItemsController@edit')->name('order-items.edit');
Route::get('/order/{order}/items', 'Orders\OrderItemsController@storedItems');
Route::post('/order/{order}/items', 'Orders\OrderItemsController@update');

Route::get('/orders/{client}/unpaid', 'Orders\ClientOrdersController@unpaid');
Route::get('/orders/{client}/debt', 'Orders\ClientOrdersController@totalDebt');


Route::resource('orders', 'OrdersController', ['except' => ['delete', 'edit']]);

Route::get('branch/{branch}/orders', 'OrdersController@filteredByBranch');
Route::get('user/{user}/orders', 'OrdersController@filteredByUser');


//Trips
Route::get('/trips/all', 'Trips\TripsController@all');
Route::resource('trips', 'Trips\TripsController',
    ['except' => ['destroy']]);

Route::get('/trip/{trip}/stored-items/edit', 'Trips\TripStoredItemsController@edit')->name('trip.edit-items');
Route::post('/trip/{trip}/status', 'Trips\TripStoredItemsController@changeStatus')->name('trip.status');
Route::get('/trip/stored-items/available', 'Trips\TripStoredItemsController@availableItems');
Route::get('/trip/{branch}/stored-items/available', 'Trips\TripStoredItemsController@availableItemsAtBranch');
Route::post('/trip/{trip}/stored-items', 'Trips\TripStoredItemsController@associateToTrip');
Route::get('/trip/{trip}/stored-items/load', 'Trips\TripStoredItemsController@editLoaded')->name('trip.edit-loaded');
Route::post('/trip/{trip}/stored-items/load', 'Trips\TripStoredItemsController@updateLoaded')->name('trip.update-loaded');
Route::get('/trip/{trip}/stored-items/unload', 'Trips\TripStoredItemsController@editUnloaded')->name('trip.edit-unloaded');
Route::post('/trip/{trip}/stored-items/unload', 'Trips\TripStoredItemsController@updateUnloaded')->name('trip.update-unloaded');
Route::get('/trip/{trip}/exchange/stored-items', 'Trips\TripStoredItemsController@changeItemsTrip')->name('trip.change-items-trip');
Route::post('/trip/{trip}/exchange/stored-items', 'Trips\TripStoredItemsController@exchangeItems')->name('trip.exchange-items');

//Till

Route::resource('payments', 'Till\Payments\PaymentsController', ['only' => 'index']);

Route::get('/pending-payments', 'Till\Payments\PendingPaymentsController@index')->name('pending-payments.index');
Route::get('/pending-payments/filtered', 'Till\Payments\PendingPaymentsController@filtered');

Route::get('/payments/all', 'Till\Payments\PaymentsController@all')->name('payments.all');
Route::get('/payments/filtered', 'Till\Payments\PaymentsController@filtered');

Route::resource('incoming-payments', 'Till\Payments\IncomingPaymentsController',
    ['except' => ['destroy']])->parameters(['incoming-payments' => 'payment']);

Route::resource('outgoing-payments', 'Till\Payments\OutgoingPaymentsController')->parameters(['outgoing-payments' => 'payment']);

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
Route::get('tariff/{tariff}/pricing', 'TariffsController@pricing');
Route::resource('tariffs', 'TariffsController', [
    'only' => ['index', 'store', 'destroy']
])->parameters(['tariffs' => 'tariff']);



//Tariff price histories
Route::get('/tariff-price-histories/all', "TariffPriceHistoriesController@all");
Route::resource('tariff-price-histories', 'TariffPriceHistoriesController', [
    'only' => ['index', 'create', 'store']
])->parameters(['tariff-price-histories' => 'history']);
//Route::get('/tariff-price-histories/{tariff}', "TariffPriceHistoriesController@lastByTariff");

//Cars
Route::get('/cars/all', 'CarsController@all');
Route::resource('cars', 'CarsController')->parameters(['cars' => 'car']);

//FuelConsumption
Route::resource('car-fuel-consumption', 'Cars\CarFuelConsumptionsController',
    ['only' => ['edit',  'update']])->parameters(['car-fuel-consumption' => 'car']);

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








