<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 06.07.2020
 */

namespace App\Services\Order;


use App\Data\Dto\Till\PaymentDto;
use App\Models\Currency;
use App\Models\Order;
use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemTripHistory;
use App\Models\Till\Account;
use App\Models\Till\Payment;
use App\Models\Till\PaymentItem;
use App\Models\Users\Client;
use App\Models\Users\TrustedUser;
use App\Services\Storage\ItemsStorageHistoryService;
use App\Services\StoredItem\StoredItemService;
use App\Services\StoredItem\StoredItemsPaymentService;
use App\Services\StoredItem\Trip\StoredItemTripHistoryService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class OrderStoredItemsDeliveryService
{
    private StoredItemsPaymentService $paymentService;

    private OrderService $orderService;

    private ItemsStorageHistoryService $storageHistoryService;

    private StoredItemTripHistoryService $tripHistoryService;

    private StoredItemService $storedItemService;

    public function __construct(
        StoredItemsPaymentService $paymentService,
        OrderService $orderService,
        ItemsStorageHistoryService $storageHistoryService,
        StoredItemTripHistoryService $tripHistoryService,
        StoredItemService $storedItemService
    )
    {
        $this->paymentService = $paymentService;
        $this->orderService = $orderService;
        $this->storageHistoryService = $storageHistoryService;
        $this->tripHistoryService = $tripHistoryService;
        $this->storedItemService = $storedItemService;
    }

    /**
     * @param Client $client
     * @param Collection $storedItemsIds that should be delivered
     * @param bool $isDebtRequested
     * @return Payment
     */
    public function deliver(Client $client, Collection $storedItemsIds, bool $isDebtRequested): Payment
    {
        $storedItems = StoredItem::with('info', 'info.billingInfo', 'info.order')
            ->whereIn('id', $storedItemsIds->unique())
            ->whereHas('info', function ($query) use ($client) {
                $query->where('owner_id', $client->id);
            })->get();

        $payment = $this->checkPayment($client, $storedItems, $isDebtRequested);

        $this->deliverStoredItems($storedItems);

        $storedItems->pluck('info.order')->unique('id')->each(function (Order $order) {
            $this->orderService->updateStatus($order);
        });

        return $payment;
    }

    private function checkPayment(Client $client, Collection $storedItems, bool $isDebtRequested): Payment
    {
        $paymentSum = $storedItems->pluck('info.billingInfo')->sum('pricePerItem');
        $dollarAccount = Currency::where('isoName', 'USD')->firstOrFail();

        /** @var Account $ownerAccount */
        $ownerAccount = $client->accounts()->where('currency_id',$dollarAccount->id)->firstOrFail();

        if ($ownerAccount->balance < $paymentSum) {
            if (!$isDebtRequested)
                abort(422, "Недостаточно средств на балансе");
            else if (!$this->checkForDebtPossibility($paymentSum, $ownerAccount))
                abort(422, "Доверительный платеж не возможен");
        }

        return $this->createPayment($paymentSum, $client, $ownerAccount, $storedItems);

    }

    private function checkForDebtPossibility(float $paymentSum, Account $account): bool
    {
        $trusted = TrustedUser::where('user_id', $account->owner_id)
            ->where('to', '>=', Carbon::now()->toDateString())
            ->first();

        if (!$trusted)
            return false;

        $overallDebt = $account->balance - $paymentSum;

        if (abs($overallDebt) > $trusted->maxDebt)
            abort(400,
                "Суммарный долг доверенного клиента превыщает максимально допустимый.
                 Текущая задолженность " . abs($overallDebt) . " USD.
                 Требуемая оплата за выбранные товары " . $paymentSum . " USD"
            );

        return true;
    }

    private function createPayment(float $paymentSum, Client $client, Account $account, Collection $storedItems): Payment
    {
        $dto = new PaymentDto([
            'branch_id' => auth()->user()->branch->id,
            'status' => 'completed',
            'payer_id' => $account->owner_id,
            'payer_type' => 'user',
            'payer_account_in_bill_currency_id' => $account->id,
            'payer_account_in_second_currency_id' => null,
//            'payer_type' => 'user',
            'payee_id' => null,
            'payee_account_in_bill_currency_id' => null,
            'payee_account_in_second_currency_id' => null,
            'payee_type' => null,
            'payment_item_id' => PaymentItem::firstOrCreate([
                'title' => 'Списание с баланса',
                'description' => 'Списание денег с баланса клиента в счет оплаты заказы'
            ])->id,
            'billAmount' => (float)$paymentSum,
            'paidAmountInBillCurrency' => (float)$paymentSum,
            'paidAmountInSecondCurrency' => 0.0,
            'bill_currency_id' => Currency::where('isoName', 'USD')->first()->id,
            'second_paid_currency_id' => null,
            'exchange_rate_id' => null,
            'comment' => 'Списание денег с баланса в счет оплаты заказа',
        ]);

        return $this->paymentService->store($dto, $client, $account, $storedItems);
    }

    private function deliverStoredItems(Collection $storedItems)
    {
        $ids = $storedItems->pluck('id');

        $this->storageHistoryService->massDelete($ids);

        $this->tripHistoryService->massDelete($ids, StoredItemTripHistory::STATUS_CANCELED);

        $this->storedItemService->massDelete($ids, StoredItem::STATUS_DELIVERED);

//        StoredItem::whereIn('id', $ids)->update([
//            'status' => StoredItem::STATUS_DELIVERED
//        ]);
    }
}
