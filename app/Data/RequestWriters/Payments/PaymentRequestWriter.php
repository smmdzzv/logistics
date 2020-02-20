<?php

namespace App\Data\RequestWriters\Payments;

use App\Data\RequestWriters\RequestWriter;
use App\Http\Requests\Till\PaymentRequest;
use App\Models\Branch;
use App\Models\Till\Account;
use App\Models\Till\Payment;
use App\User;


class PaymentRequestWriter extends RequestWriter
{
    /**
     * @var User|Branch $id
     */
    protected $payer;

    /**
     * @var User|Branch $id
     */
    protected $payee;

    protected ?Account $payerAccount;
    protected ?Account $payeeAccount;

    protected Payment $payment;

    /**
     * PaymentRequestWriter constructor.
     * @param PaymentRequest $request
     */
    public function __construct(PaymentRequest $request)
    {
        parent::__construct(null, $request);
    }

    /**
     * @return Payment
     */
    function write()
    {
        if ($this->request->get('id')) {
            $this->payment = Payment::findOrFail($this->request->get('id'));

            if ($this->payment->status === 'completed')
                abort(403, 'Платеж уже проведен');

            $this->payment->status = $this->request->get('status');
            $this->payment->cashier_id = auth()->user()->id;
            $this->payment->branch_id = auth()->user()->branch->id;
            $this->payment->save();

            $this->setSubjectsAndAccounts();

            $this->checkPayerBalance();
        } else {
            $this->getPaymentSubjects();

            $this->getAccounts();

            $this->checkPayerBalance();

            $this->createPayment();
        }

        if ($this->payment->status === 'completed') {
            $this->updatePayerBalance();
            $this->updatePayeeBalance();
        }

        return $this->payment;
    }

    protected function setSubjectsAndAccounts()
    {
        $this->payer = $this->payment->payer;
        $this->payee = $this->payment->payee;

        $this->payerAccount = $this->payment->payerAccount;
        $this->payeeAccount = $this->payment->payeeAccount;
    }

    protected function getPaymentSubjects()
    {
        $this->payer = $this->getSubject($this->request->get('payer_type'), $this->request->get('payer'));
        $this->payee = $this->getSubject($this->request->get('payee_type'), $this->request->get('payee'));
    }

    protected function getAccounts()
    {
        $this->payerAccount = $this->getSubjectAccount($this->payer);
        $this->payeeAccount = $this->getSubjectAccount($this->payee);
    }

    protected function checkPayerBalance()
    {
        if ($this->payerAccount) {
            $amount = isset($this->payment) ? $this->payment->paidAmount : $this->request->get('paidAmount');
            $diff = $this->payerAccount->balance - $amount;
            if ($diff < 0)
                abort(422, 'Недостаточно средств на балансе плательщика');
        }
    }

    protected function getSubject($type, $id)
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

    protected function getSubjectAccount($subject)
    {
        $account = null;

        if ($subject instanceof Branch) {
            $account = $subject->accounts()->where('currency_id', $this->request->get('paidCurrency'))->firstOrFail();
        }

        return $account;
    }

    protected function createPayment()
    {
        $this->payment = Payment::create([
            'branch_id' => auth()->user()->branch->id,
            'cashier_id' => auth()->user()->id,
            'status' => $this->request->get('status'),
            'prepared_by_id' => $this->request->get('status') === 'pending' ? auth()->user()->id : null,
            'payer_id' => $this->request->get('payer'),
            'payer_account_id' => $this->payerAccount === null ? null : $this->payerAccount->id,
            'payer_type' => $this->request->get('payer_type'),
            'payee_id' => $this->request->get('payee'),
            'payee_account_id' => $this->payeeAccount === null ? null : $this->payeeAccount->id,
            'payee_type' => $this->request->get('payee_type'),
            'payment_item_id' => $this->request->get('paymentItem'),
            'billAmount' => $this->request->get('billAmount'),
            'paidAmount' => $this->request->get('paidAmount'),
            'bill_currency_id' => $this->request->get('billCurrency'),
            'paid_currency_id' => $this->request->get('paidCurrency'),
            'exchange_rate_id' => $this->request->get('exchangeRate'),
            'comment' => $this->request->get('comment'),
        ]);
    }

    protected function updatePayerBalance()
    {
        if ($this->payerAccount) {
            $this->payerAccount->balance -= $this->payment->paidAmount;
            $this->payerAccount->save();
        }
    }

    protected function updatePayeeBalance()
    {
        if ($this->payeeAccount) {
            $this->payeeAccount->balance += $this->payment->paidAmount;
            $this->payeeAccount->save();
        }
    }
}
