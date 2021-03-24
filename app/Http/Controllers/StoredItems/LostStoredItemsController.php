<?php

namespace App\Http\Controllers\StoredItems;


use App\Data\RequestWriters\Payments\PaymentRequestWriterFabric;
use App\Http\Controllers\BaseController;
use App\Models\Currency;
use App\Models\StoredItems\StoredItem;
use App\Models\Till\Payment;
use App\Models\Till\PaymentItem;

class LostStoredItemsController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:admin');
    }

    public function store(StoredItem $storedItem){
        $data = request()->validate([
            'compensation' => 'required|numeric'
        ]);

        $storedItem->update(['status' => StoredItem::STATUS_LOST]);

        $paymentData = [
            'status' => Payment::STATUS_COMPLETED,
            'payer' => auth()->user()->branch->id,
            'payer_type' => 'branch',
            'payee' => $storedItem->info->owner->id,
            'payee_type' => 'user',
            'paymentItem' => PaymentItem::where('title', 'Бонус')->firstOrFail()->id,
            'billAmount' =>  $data['compensation'],
            'paidAmountInBillCurrency' => $data['compensation'],
            'billCurrency' => Currency::where('isoName', 'USD')->firstOrFail()->id,
            'comment' => 'Бонус в счет утерянного товара ' . $storedItem->code
        ];

        $fabric = new PaymentRequestWriterFabric($paymentData);
        $writer = $fabric->getWriter();
        $payment = $writer->write();

        return $payment->id;
    }

//    public function store(LostStoredItemsRequest $request)
//    {
//        $storedItems = StoredItem::whereIn('id', $request['storedItems'])->get();
//
//        if($storedItems->count() === 0)
//            abort(422, 'Выбранные товары уже выданы');
//
//        $owner = $storedItems[0]->info->owner;
//
//        $paymentData = [
//            'status' => 'pending',
//            'payer' => $request->user()->branch->id,
//            'payer_type' => 'branch',
//            'payee' => $owner->id,
//            'payee_type' => 'user',
//            'paymentItem' => PaymentItem::where('title', 'Выдача наличных средств')->firstOrFail()->id,
//            'billAmount' => $request['compensation'],
//            'paidAmountInBillCurrency' => $request['compensation'],
//            'billCurrency' => Currency::where('isoName', 'USD')->firstOrFail()->id,
//            'comment' => 'Возрат денег в счет утерянных товаров',
//        ];
//
//        $fabric = new PaymentRequestWriterFabric($paymentData);
//        $writer = $fabric->getWriter();
//        $payment = $writer->write();
//
//        foreach ($storedItems as $storedItem) {
//            LostStoredItem::create([
//                'stored_item_id' => $storedItem->id,
//                'payment_id' => $payment->id
//            ]);
//
//            if ($storedItem->tripHistory)
//                $storedItem->tripHistory->delete();
//            if ($storedItem->storageHistory)
//                $storedItem->storageHistory->delete();
//            $storedItem->delete();
//        }
//
//        return $payment->id;
//    }
}
