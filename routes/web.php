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

use Illuminate\Support\Facades\Route;

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

Route::resource('trusted-user', 'Users\TrustedUserController');

//Profile
Route::get('profile/{user}', 'ProfilesController@show')->name('profile.show');

//Order
//Route::get('/orders/all', 'OrdersController@all')->name('order.all');
Route::get('/orders/filtered', 'OrdersController@filtered')->name('order.filtered');
Route::get('/orders/{client}/active', 'OrdersController@activeOrders')->name('client.orders.active');

Route::post('/order/{order}/update-price', 'Orders\OrderPriceController@update')->name('order-price.update');

Route::get('/orders/items/edit', 'Orders\OrderItemsController@edit')->name('order-items.edit');
Route::get('/order/{order}/items', 'Orders\OrderItemsController@storedItems');
Route::get('/order/{order}/unpaid-items', 'Orders\OrderItemsController@unpaidStoredItems');
Route::get('/order/{order}/payments', 'Orders\OrderItemsController@orderPayments');
Route::post('/deliver/{order}/items', 'Orders\OrderItemsController@deliver');
Route::post('/deliver/{order}/items/pending-payment', 'Orders\OrderItemsController@storePaymentRequest');

Route::get('/orders/{client}/unpaid', 'Orders\ClientOrdersController@unpaid');
Route::get('/orders/{client}/debt', 'Orders\ClientOrdersController@totalDebt');
Route::get('/orders/{clientCode}/statistics', 'Orders\ClientOrdersController@getStatistics');

Route::resource('orders', 'OrdersController', ['except' => ['delete']]);

Route::get('branch/{branch}/orders', 'OrdersController@filteredByBranch');
Route::get('user/{user}/orders', 'OrdersController@filteredByUser');


//Trips
Route::get('/trips/all', 'Trips\TripsController@all');
Route::post('/trip/{trip}/status', 'Trips\TripsController@changeStatus')->name('trip.status');
Route::resource('trips', 'Trips\TripsController',
    ['except' => ['destroy']]);

Route::get('/trip/{trip}/stored-items/edit', 'Trips\TripStoredItemsController@edit')->name('trip.edit-items');
Route::get('/trip/{trip}/stored-items/generate', 'Trips\TripStoredItemsController@generate')->name('trip.generate-items-list');
Route::get('/trip/stored-items/available', 'Trips\TripStoredItemsController@availableItems');
Route::get('/trip/{branch}/stored-items/available', 'Trips\TripStoredItemsController@availableItemsAtBranch');
Route::post('/trip/{trip}/stored-items', 'Trips\TripStoredItemsController@associateToTrip');
Route::get('/trip/{trip}/stored-items/load', 'Trips\TripStoredItemsController@editLoaded')->name('trip.edit-loaded');
Route::post('/trip/{trip}/stored-items/load', 'Trips\TripStoredItemsController@updateLoaded')->name('trip.update-loaded');
Route::get('/trip/{trip}/stored-items/unload', 'Trips\TripStoredItemsController@editUnloaded')->name('trip.edit-unloaded');
Route::post('/trip/{trip}/stored-items/unload', 'Trips\TripStoredItemsController@updateUnloaded')->name('trip.update-unloaded');
Route::get('/trip/{trip}/exchange/stored-items', 'Trips\TripStoredItemsController@changeItemsTrip')->name('trip.change-items-trip');
Route::post('/trip/{trip}/exchange/stored-items', 'Trips\TripStoredItemsController@exchangeItems')->name('trip.exchange-items');

//Customs
Route::resource('customs-code', 'Customs\CustomsCodeController')->parameters(['customs-code' => 'code']);

//Till

Route::get('/pending-payments', 'Till\Payments\PendingPaymentsController@index')->name('pending-payments.index');
Route::get('/pending-payments/filtered', 'Till\Payments\PendingPaymentsController@filtered');

Route::get('/reports/expenses', 'Till\Reports\ClientExpenseReportsController@index')->name('expense-report.index');
Route::get('/reports/expenses/generate', 'Till\Reports\ClientExpenseReportsController@generateReport');

Route::get('/payments/all', 'Till\Payments\PaymentsController@all')->name('payments.all');
Route::get('/payments/filtered', 'Till\Payments\PaymentsController@filtered');
Route::post('/payment', 'Till\Payments\PaymentsController@storeOrUpdate');
Route::resource('payment', 'Till\Payments\PaymentsController', ['except' => ['store', 'update']]);

//Route::resource('incoming-payments', 'Till\Payments\IncomingPaymentsController',
//    ['except' => ['destroy']])->parameters(['incoming-payments' => 'payment']);

Route::resource('outgoing-payments', 'Till\Payments\OutgoingPaymentsController')->parameters(['outgoing-payments' => 'payment']);

//Route::resource('payments', 'Till\Payments\PaymentsController', ['only' => ['index', 'show']])->parameters(['payments' => 'payment']);

//StoredItems
//Route::get('/stored', 'StoredItemsController@index')->name('stored.index');
Route::get('/stored/all', 'StoredItemsController@all')->name('stored.all');
Route::get('/stored/{storedItem}', 'StoredItemsController@show')->name('stored.show');
Route::get('/{branch}/stored', 'StoredItemsController@filteredByBranch');
//Route::post('/stored/trip/{trip}', 'StoredItemsController@associateToTrip');

//StoredItemInfo
//Route::get('/stored-item-info/available/filtered', 'StoredItemInfo\StoredItemInfoController@availableStoredItemInfos');
Route::get('/stored-item-info', 'StoredItemInfo\StoredItemInfoController@index')->name('stored-item-infos.index');
Route::get('/stored-item-info/statistics', 'StoredItemInfo\StoredItemInfoController@getClientStat');
Route::get('/stored-item-info/filtered', 'StoredItemInfo\StoredItemInfoController@storedItemInfos');

//Shops
Route::resource('shop', 'Shops\ShopsController', ['only' => ['create', 'store']]);

//Settings
Route::get('/settings', 'AppSettingsController@show');
Route::post('/settings/branch', 'AppSettingsController@storeBranch');
Route::post('/settings/position', 'AppSettingsController@storePosition');

//Tariffs
Route::get('tariff/{tariff}/pricing', 'Tariffs\TariffsController@pricing');
Route::resource('tariffs', 'Tariffs\TariffsController', [
    'only' => ['index', 'store', 'destroy']
])->parameters(['tariffs' => 'tariff']);


//Tariff price histories
Route::get('/tariff-price-histories/all', "Tariffs\TariffPriceHistoriesController@all");
Route::post('/tariff-price-history/{history}/orders/update-price', "Orders\OrderPriceController@updateByTariffPriceHistory")->name('update-price.tariff-price-history');
Route::resource('tariff-price-histories', 'Tariffs\TariffPriceHistoriesController')->parameters(['tariff-price-histories' => 'history']);
//Route::get('/tariff-price-histories/{tariff}', "TariffPriceHistoriesController@lastByTariff");

//Cars
Route::get('/cars/all', 'CarsController@all');
Route::resource('cars', 'CarsController')->parameters(['cars' => 'car']);

//FuelConsumption
Route::resource('car-fuel-consumption', 'Cars\CarFuelConsumptionsController',
    ['only' => ['edit', 'update']])->parameters(['car-fuel-consumption' => 'car']);

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

Route::get('/exchange-history/rate/{from}/{to}', 'Till\MoneyExchangesController@exchangeRate');

Route::post('/exchange-money', 'Till\MoneyExchangesController@exchange')->name('money-exchange.exchange');
Route::get('/exchange-money', 'Till\MoneyExchangesController@exchanger')->name('money-exchange.exchanger');

//Items
Route::get('/items/all', 'ItemsController@all');
Route::get('/items/all/eager', 'ItemsController@allEager');
Route::resource('items', 'ItemsController', ['only' => ['index', 'create', 'store']])->parameters(['items' => 'item']);

//Lost and found
//Route::resource('lost-items', 'LostAndFound\LostItemsController')->parameters(['lost-items' => 'item']);

Route::post('/lost-stored-items', 'StoredItems\LostStoredItemsController@store');

Route::get('/search/user/{userInfo}', 'SearchController@findUsersByInfo');

//Legal Entities
Route::get('/legal-entity/accounts', 'Till\Accounts\BranchesAccountsController@index')->name('branches.accounts.index');

//Accounts
Route::get('/accounts/{holder}', 'Till\Accounts\AccountsController@holderAccounts');

//Countries
Route::get('/countries', 'CountriesController@all');








