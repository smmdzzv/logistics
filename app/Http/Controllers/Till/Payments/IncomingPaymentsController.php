<?php

namespace App\Http\Controllers\Till\Payments;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Till\PaymentRequest;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\LegalEntities\LegalEntity;
use App\Models\Order;
use App\Models\Till\MoneyExchange;
use App\Models\Till\Payment;
use App\Models\Till\PaymentItem;
use App\User;
use Illuminate\Database\Eloquent\Builder;

class IncomingPaymentsController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:cashier, manager');
    }

    public function all()
    {
        return Payment::with('accountTo', 'payer', 'currency', 'paymentItem')
            ->whereHas('paymentItem', function (Builder $query) {
                $query->where('type', 'in');
            })->latest()->paginate($this->pagination());
    }

    public function filteredByBranch(Branch $branch)
    {
        return $branch->payments()
            ->with('accountTo', 'payer', 'currency', 'paymentItem')
            ->paginate($this->pagination());
    }

    public function create()
    {
        $accountTo = LegalEntity::first()->accounts()->with('currency')->first();
        $currencies = Currency::all();
        return view('till.payments.incoming.create', compact('accountTo', 'currencies'));
    }

    public function store(PaymentRequest $request)
    {
        $accountTo = $this->getAccountTo($request);

        $payment = new Payment();
        $payment->branchId = auth()->user()->branch->id;
        $payment->cashierId = auth()->id();
        $payment->currencyId = $request->input('currencyId');
        $payment->payerId = $request->input('payerId');
        $payment->paymentItemId = $request->input('paymentItemId');
        $payment->accountToId = $accountTo->id;
        $payment->exchangeId = $request->input('exchangeId');
        $payment->amount = round($request->input('amount'), 2);
        $payment->comment = $request->input('comment');
        $payment->save();

        //Change balance of account to
        $sum = $payment->amount;
        $exchange = MoneyExchange::find($payment->exchangeId);
        if ($exchange)
            $sum = $payment->amount * $exchange->coefficient;

        $payment->accountTo->balance += $sum;
        $payment->accountTo->save();

        //Attach payment to Order
        $item = PaymentItem::find($payment->paymentItemId);
        if (mb_strtolower($item->title) === "оплата заказа") {
            $order = Order::find(request()->input('orderId'));
            $order->payment()->associate($payment);
            $order->save();
        }

        return redirect()->route('payments.index');
    }

    private function getAccountTo(PaymentRequest $request){
        $accountTo = null;
        $item = PaymentItem::find($request->input('paymentItemId'));

        if($item->title === 'Пополнение баланса')
            $accountTo = User::find($request->input('payerId'))->accounts()->first();
        else
            $accountTo = LegalEntity::first()->accounts()->first();

        return $accountTo;
    }
}
