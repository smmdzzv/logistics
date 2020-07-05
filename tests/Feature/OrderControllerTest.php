<?php

namespace Tests\Feature;

use App\Common\PasswordGenerator;
use App\Models\BillingInfo;
use App\Models\Branch;
use App\Models\Branches\Storage;
use App\Models\Customs\CustomsCode;
use App\Models\Customs\CustomsCodeTax;
use App\Models\Order;
use App\Models\StoredItems\Item;
use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemInfo;
use App\Models\Tariff;
use App\Models\TariffPriceHistory;
use App\StoredItems\StorageHistory;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 03.07.2020
 */
class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_store()
    {
        $branch = Branch::create([
            'name' => 'Душанбе',
            'country' => '01ec7gk3ac4ekfde5vfy5p0s23'
        ]);

        $storage = Storage::create([
            'branch_id' => $branch->id,
            'name' => 'Ленинский'
        ]);

        $client = User::create([
            'code' => '123456',
            'password' => Hash::make(PasswordGenerator::generate()),
            'branch_id' => $branch->id
        ]);

        $employee = User::create([
            'code' => '52525',
            'password' => Hash::make(PasswordGenerator::generate()),
            'branch_id' => $branch->id
        ]);

        Auth::shouldReceive('id')->andReturn($employee->id);

        $this->withoutMiddleware();
        $this->withoutExceptionHandling();

        $item = Item::create([
            'name' => 'Одежда',
            'calculateByNormAndWeight' => true,
            'applyDiscount' => true
        ]);

        $tariff = Tariff::create([
            'name' => 'Одежду',
            'branch_id' => $branch->id
        ]);

        $tariffPricing = TariffPriceHistory::create([
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
            'tariff_id' => $tariff->id
        ]);

        $customsCode = CustomsCode::create([
            'name' => 'Одежда',
            'code' => '15755484'
        ]);

        $customsCodeTax = CustomsCodeTax::create([
            'customs_code_id' => $customsCode->id,
            'price' => 100,
            'interestRate' => 5,
            'vat' => 18,
            'totalRate' => 22.5,
            'isCalculatedByPiece' => false
        ]);

        $this->withoutExceptionHandling();

        $response = $this->post('/orders', [
            'clientCode' => $client->code,
            'branch_id' => $branch->id,
            'storedItemInfos' => [
                0 => [
                    'width' => 0.25,
                    'height' => 0.55,
                    'length' => 0.65,
                    'weight' => 3,
                    'shop' => 'Test shop',
                    'count' => 10,
                    'item_id' => $item->id,
                    'tariff_id' => $tariff->id,
                    'tariff_price_history_id' => $tariffPricing->id,
                    'branch_id' => $branch->id,
                    'customs_code_tax_id' => $customsCodeTax->id
                ]
            ],
            'customPrices' => [null]
        ]);

        $response->assertStatus(201);

        $this->assertCount(1, Order::all());

        $this->assertCount(1, StoredItemInfo::all());

        $this->assertCount(1,
            StoredItemInfo::with('order')->get()->pluck('order.id')->unique());

        $this->assertEquals(Order::first()->id,
            StoredItemInfo::with('order')->get()->pluck('order.id')->unique()->first());

        $this->assertCount(1, BillingInfo::all());

        $this->assertCount(10, StoredItem::all());

        $this->assertCount(10, StorageHistory::all());

        $this->assertCount(1, StoredItem::all()->pluck('status')->unique());

        $this->assertEquals('stored', StoredItem::all()->pluck('status')->unique()->first());
    }
}
