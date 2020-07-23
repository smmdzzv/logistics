<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 06.07.2020
 */

namespace App\Services\StoredItem;


use App\Data\Dto\Till\PaymentDto;
use App\Models\StoredItems\ItemsSelection;
use App\Models\Till\Account;
use App\Models\Till\Payment;
use App\Models\Users\Client;
use App\Services\Till\Payment\PaymentService;
use Illuminate\Support\Collection;

class StoredItemsPaymentService
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(PaymentDto $dto, Client $client, Account $account, Collection $storedItems, bool $updateBalances = true): Payment
    {
        $payment = $this->paymentService->store($dto);

        $clientSelection = ItemsSelection::create([
            'user_id' => $client->id
        ]);

        /** @var ItemsSelection $clientSelection */
        $clientSelection->storedItems()->sync($storedItems);

        if($updateBalances){
            $account->balance -= $payment->billAmount;
            $account->save();

            $payment->clientDebt -= $payment->billAmount;
            $payment->placesLeft -= $storedItems->count();
        }

        $payment->client_items_selection_id = $clientSelection->id;

        $payment->save();

//
//        $storedItems->each(function ($item) use ($orderPayment) {
//            OrderPaymentItem::create([
//                'stored_item_id' => $item->id,
//                'order_payment_id' => $orderPayment->id
//            ]);
//        });

//        $orderPayment = OrderPayment::create([
//            'order_id' => $order->id,
//            'payment_id' => $payment->id
//        ]);
//
//        $storedItems->each(function ($item) use ($orderPayment) {
//            OrderPaymentItem::create([
//                'stored_item_id' => $item->id,
//                'order_payment_id' => $orderPayment->id
//            ]);
//        });

        return $payment;
    }
}
