<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 06.07.2020
 */

namespace App\Services\StoredItem;


use App\Data\Dto\Till\PaymentDto;
use App\Models\Order;
use App\Models\Order\OrderPayment;
use App\Models\Order\OrderPaymentItem;
use App\Models\Till\Account;
use App\Models\Till\Payment;
use App\Services\Till\Payment\PaymentService;
use Illuminate\Support\Collection;

class StoredItemsPaymentService
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(PaymentDto $dto, Order $order, Account $account, Collection $storedItems): Payment
    {
        $payment = $this->paymentService->store($dto);
        $payment->save();

        $payment->clientDebt -= $payment->billAmount;
        $payment->placesLeft -= $storedItems->count();
        $payment->save();

        $account->balance -= $payment->billAmount;
        $account->save();

        $orderPayment = OrderPayment::create([
            'order_id' => $order->id,
            'payment_id' => $payment->id
        ]);

        $storedItems->each(function ($item) use ($orderPayment) {
            OrderPaymentItem::create([
                'stored_item_id' => $item->id,
                'order_payment_id' => $orderPayment->id
            ]);
        });

        return $payment;
    }
}
