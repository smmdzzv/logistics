<?php


namespace App\Http\Controllers\StoredItems;


use App\Data\RequestWriters\Payments\BalanceReplenishmentPaymentRequestWriter;
use App\Data\RequestWriters\Payments\PaymentRequestWriter;
use App\Data\RequestWriters\Payments\PaymentRequestWriterFabric;
use App\Http\Controllers\BaseController;
use App\Http\Requests\StoredItem\LostStoredItemsRequest;
use App\Models\Currency;
use App\Models\LostAndFound\LostStoredItem;
use App\Models\StoredItems\StoredItem;
use App\Models\Till\PaymentItem;

class LostStoredItemsController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:admin');
    }

    public function store(LostStoredItemsRequest $request)
    {
        $storedItems = StoredItem::whereIn('id', $request['storedItems'])->get();

        if($storedItems->count() === 0)
            abort(422, 'Выбранные товары уже выданы');

        $owner = $storedItems[0]->info->owner;

        $paymentData = [
            'status' => 'pending',
            'payer' => $request->user()->branch->id,
            'payer_type' => 'branch',
            'payee' => $owner->id,
            'payee_type' => 'user',
            'paymentItem' => PaymentItem::where('title', 'Выдача наличных средств')->firstOrFail()->id,
            'billAmount' => $request['compensation'],
            'paidAmountInBillCurrency' => $request['compensation'],
            'billCurrency' => Currency::where('isoName', 'USD')->firstOrFail()->id,
            'comment' => 'Возрат денег в счет утерянных товаров',
        ];

        $fabric = new PaymentRequestWriterFabric($paymentData);
        $writer = $fabric->getWriter();
        $payment = $writer->write();

        foreach ($storedItems as $storedItem) {
            LostStoredItem::create([
                'stored_item_id' => $storedItem->id,
                'payment_id' => $payment->id
            ]);

            if ($storedItem->tripHistory)
                $storedItem->tripHistory->delete();
            if ($storedItem->storageHistory)
                $storedItem->storageHistory->delete();
            $storedItem->delete();
        }

        return $payment->id;
    }
}
