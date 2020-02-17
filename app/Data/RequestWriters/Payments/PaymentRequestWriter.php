<?php

namespace App\Data\RequestWriters\Payments;

use App\Data\RequestWriters\RequestWriter;
use App\Http\Requests\Till\PaymentRequest;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\Till\Account;
use App\Models\Till\Payment;
use App\Models\Till\PaymentItem;
use App\User;


class PaymentRequestWriter extends RequestWriter
{
    /**
     * @var User or Branch $id
     */
    private $payer;

    /**
     * @var User or Branch $id
     */
    private $payee;

    private ?Account $payerAccount;
    private ?Account $payeeAccount;

    private Payment $payment;

    /**
     * PaymentRequestWriter constructor.
     * @param PaymentRequest $request
     */
    public function __construct(PaymentRequest $request)
    {
        parent::__construct(null, $request);
    }

    function write()
    {
        $this->getPaymentSubjects();

        $this->createPayment();

        if ($this->payment->status === 'completed')
            $this->updateAccountsBalance();

        return $this->payment;
    }

    private function getPaymentSubjects()
    {
        $this->payer = $this->getSubject($this->request->get('payerType'), $this->request->get('payer'));
        $this->payee = $this->getSubject($this->request->get('payeeType'), $this->request->get('payee'));

        $this->payerAccount = $this->getSubjectAccount($this->payer);
        $this->payeeAccount = $this->getSubjectAccount($this->payee);
        //Users have only dollar account. All operations should be in dollars
        if ($this->payerAccount === null
            && $this->payer instanceof User
            && PaymentItem::where('id', $this->request->get('paymentItem'))->where('title', 'Пополнение баланса')->first()
            && Currency::find($this->request->get('billCurrency'))->isoName === 'USD')
            $this->payerAccount = $this->payer->accounts()->dollarAccount();
    }

    private function getSubject($type, $id)
    {
        $subject = null;

        switch ($type) {
            case 'user':
                $subject = User::find($id);
                break;
            case 'branch':
                $subject = Branch::find($id);
                break;
        }

        return $subject;
    }

    private function getSubjectAccount($subject)
    {
        $account = null;

//        $paymentItem = PaymentItem::balanceOperations()
//            ->where('id', $this->request->get('paymentItem'))
//            ->first();

        if ($subject instanceof Branch) {
            $account = $subject->accounts()->where('currency_id', $this->request->get('paidCurrency'))->firstOrFail();
        }
//        elseif($subject instanceof User && $paymentItem){
//            $account = $subject->accounts()->dollarAccount();
//        }

        return $account;
    }

    private function createPayment()
    {
        $this->payment = Payment::create([
            'branch_id' => auth()->user()->branch->id,
            'cashier_id' => auth()->user()->id,
            'status' => $this->request->get('status'),
            'prepared_by_id' => $this->request->get('status') === 'pending' ? auth()->user()->id : null,
            'payer_id' => $this->request->get('payer'),
            'payer_account_id' => $this->payerAccount === null ? null : $this->payerAccount->id,
            'payerType' => $this->request->get('payerType'),
            'payee_id' => $this->request->get('payee'),
            'payee_account_id' => $this->payeeAccount === null ? null : $this->payeeAccount->id,
            'payeeType' => $this->request->get('payeeType'),
            'payment_item_id' => $this->request->get('paymentItem'),
            'billAmount' => $this->request->get('billAmount'),
            'paidAmount' => $this->request->get('paidAmount'),
            'bill_currency_id' => $this->request->get('billCurrency'),
            'paid_currency_id' => $this->request->get('paidCurrency'),
            'exchange_rate_id' => $this->request->get('exchangeRate'),
            'comment' => $this->request->get('comment'),
        ]);
    }

    private function updateAccountsBalance()
    {
        if ($this->payerAccount) {
            if ($this->payment->paymentItem->title === "Пополнение баланса")
                $this->payerAccount->balance += $this->payment->paidAmount;
            else
                $this->payerAccount->balance -= $this->payment->paidAmount;
            $this->payerAccount->save();
        }

        if ($this->payeeAccount) {
            $this->payeeAccount->balance += $this->payment->paidAmount;
            $this->payeeAccount->save();
        }
    }


//    function write()
//    {
//        return $this->writePayment();
//    }
//
//    private function writePayment()
//    {
//        $this->input->payment['branchId'] = $this->input->branchId;
//        $this->input->payment['cashierId'] = $this->input->cashierId;
//
//        if (!isset($this->input->payment['currencyId'])) {
//            $this->data->duobAccount = LegalEntity::first()->accounts()->with('currency')->first();
//            $this->input->payment['currencyId'] = $this->data->duobAccount->currency->id;
//        }
//
//        $filter = ['orderId', 'accountTo', 'accountFrom', 'recipient'];
//        $data = array_filter($this->input->payment, function ($key) use ($filter) {
//            return !in_array($key, $filter);
//        }, ARRAY_FILTER_USE_KEY);
//
//        if(isset($data['id'])){
//            $this->saved->payment = Payment::findOrFail($data['id']);
//            $this->saved->payment->fill($data);
//        }
//        else
//            $this->saved->payment = new Payment($data);
//
//        $this->saved->payment->save();
//    }
}
