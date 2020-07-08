<?php /** @noinspection DuplicatedCode */

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 06.07.2020
 */

namespace Tests\Feature;


use App\Common\PasswordGenerator;
use App\Models\Branch;
use App\Models\Branches\Storage;
use App\Models\Currency;
use App\Models\Customs\CustomsCode;
use App\Models\Customs\CustomsCodeTax;
use App\Models\Order;
use App\Models\Role;
use App\Models\StoredItems\Item;
use App\Models\StoredItems\StoredItem;
use App\Models\Tariff;
use App\Models\TariffPriceHistory;
use App\Models\Till\Account;
use App\Models\Till\ExchangeRate;
use App\Models\Till\Payment;
use App\StoredItems\StorageHistory;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StoredItemsDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_order_delivery()
    {
        $this->withoutExceptionHandling();

        $data = $this->create_test_order();

        $order = Order::first();

        $response = $this->actingAs($data['employee'])
            ->post('/client/' . $order->owner->id . '/delivered-stored-items', [
                'storedItems' => $order->storedItems->pluck('id')->all(),
                'isDebtRequested' => 1
            ]);


        $this->assertCount(1, Order::all());

        $this->assertEquals(Order::STATUS_COMPLETED, Order::first()->status);

        $this->assertCount(0, StorageHistory::all());

        $this->assertCount(10, StorageHistory::onlyTrashed()->get());

        $this->assertCount(10, StoredItem::all());

        $statuses = StoredItem::all()->pluck('status')->unique();

        $this->assertCount(1, $statuses);

        $this->assertEquals(StoredItem::STATUS_DELIVERED, $statuses->first());

        $this->assertCount(1, Payment::all());

        $this->assertNotEquals(null, Payment::first()->clientItemsSelection);

        $this->assertCount(10, Payment::first()->clientItemsSelection->storedItems);
    }

    private function create_test_order($itemsCount = 10)
    {
        $data = [];
        $data['branch'] = Branch::create([
            'name' => 'Душанбе',
            'country' => '01ec7gk3ac4ekfde5vfy5p0s23'
        ]);

        $data['storage'] = Storage::create([
            'branch_id' => $data['branch']->id,
            'name' => 'Ленинский'
        ]);

        $data['client'] = User::create([
            'code' => '123456',
            'password' => Hash::make(PasswordGenerator::generate()),
            'branch_id' => $data['branch']->id,
        ]);

        $data['currency'] = Currency::create([
            'isoName' => 'USD',
            'name' => 'dollar',
            'shortName' => 'usd',
            'country_id' => 'test id'
        ]);

        $data['tjs_currency'] = Currency::create([
            'isoName' => 'TJS',
            'name' => 'somoni',
            'shortName' => 'tjs',
            'country_id' => 'test id2'
        ]);

        $data['exchange_rate'] = ExchangeRate::create([
            'from_currency_id' => $data['currency']->id,
            'to_currency_id' => $data['tjs_currency']->id,
            'coefficient' => 10
        ]);

        $account = new Account([
            'currency_id' => $data['currency']->id,
            'balance' => 10000,
            'description' => 'Долларовый счет пользователя'
        ]);

        $data['client']->accounts()->save($account);

        $data['employee'] = User::create([
            'code' => '52525',
            'password' => Hash::make(PasswordGenerator::generate()),
            'branch_id' => $data['branch']->id,
        ]);

        $data['role'] = Role::create([
            'name' => 'admin',
            'title' => 'admin',
            'description' => 'admin'
        ]);

        $data['role']->users()->attach($data['employee']);

        $data['item'] = Item::create([
            'name' => 'Одежда',
            'calculateByNormAndWeight' => true,
            'applyDiscount' => true
        ]);

        $data['tariff'] = Tariff::create([
            'name' => 'Одежда',
            'branch_id' => $data['branch']->id,
        ]);

        $data['tariffPricing'] = TariffPriceHistory::create([
            'created_at' => '2020-07-05 08:03:11',
            'lowerLimit' => 100,
            'mediumLimit' => 100,
            'upperLimit' => 100,
            'discountForLowerLimit' => 100,
            'discountForMediumLimit' => 100,
            'pricePerCube' => 100,
            'agreedPricePerKg' => 100,
            'pricePerExtraKg' => 100,
            'maxWeightPerCube' => 100,
            'maxCubage' => 100,
            'maxWeight' => 100,
            'totalMoney' => 100,
            'tariff_id' => $data['tariff']->id
        ]);

        $data['customsCode'] = CustomsCode::create([
            'name' => 'Одежда',
            'code' => '15755484'
        ]);

        $data['customsCodeTax'] = CustomsCodeTax::create([
            'customs_code_id' => $data['customsCode']->id,
            'price' => 100,
            'interestRate' => 5,
            'vat' => 18,
            'totalRate' => 22.5,
            'isCalculatedByPiece' => false
        ]);

        $response = $this->actingAs($data['employee'])
            ->post('/orders', [
                'clientCode' => $data['client']->code,
                'branch_id' => $data['branch']->id,
                'storedItemInfos' => [
                    0 => [
                        'width' => 0.25,
                        'height' => 0.55,
                        'length' => 0.65,
                        'weight' => 3,
                        'shop' => 'Test shop',
                        'count' => $itemsCount,
                        'item_id' => $data['item']->id,
                        'tariff_id' => $data['tariff']->id,
                        'branch_id' => $data['branch']->id,
                        'customs_code_id' => $data['customsCode']->id
                    ]
                ],
                'customPrices' => [null]
            ]);

        $response->assertStatus(201);

        return $data;
    }
}
