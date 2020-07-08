<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 06.07.2020
 */

namespace App\Services\StoredItem;


use App\Data\Dto\Till\PaymentDto;
use App\Models\StoredItems\ClientItemsSelection;
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

    public function store(PaymentDto $dto, Client $client, Account $account, Collection $storedItems): Payment
    {
        $payment = $this->paymentService->store($dto);

        $clientSelection = ClientItemsSelection::create([
            'client_id' => $client->id
        ]);

        /** @var ClientItemsSelection $clientSelection */
        $clientSelection->storedItems()->sync($storedItems);

        $payment->client_items_selection_id = $clientSelection->id;
        $payment->clientDebt -= $payment->billAmount;
        $payment->placesLeft -= $storedItems->count();
        $payment->save();

        $account->balance -= $payment->billAmount;
        $account->save();

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
