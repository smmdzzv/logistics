<?php

namespace App\Data\RequestWriters\Payments;

use App\Data\RequestWriters\RequestWriter;
use App\Http\Requests\Till\PaymentRequest;
use App\Models\Branch;
use App\Models\Till\Account;
use App\Models\Till\ExchangeRate;
use App\Models\Till\Payment;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;


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

    protected ?Account $payerAccountInBillCurrency = null;
    protected ?Account $payeeAccountInBillCurrency = null;
    protected ?Account $payerAccountInSecondCurrency = null;
    protected ?Account $payeeAccountInSecondCurrency = null;

    protected Payment $payment;

    /**
     * PaymentRequestWriter constructor.
     * @param $request
     */
    public function __construct(array $request)
    {
        parent::__construct(null, $request);
    }

    /**
     * @return Payment
     */
    function write()
    {
        $this->createCustomExchangeRate();

        if (isset($this->request['id'])) {
            $this->payment = Payment::findOrFail($this->request['id']);

            if ($this->payment->status === 'completed')
                abort(403, 'Платеж уже проведен');

            $this->setSubjects();

            $this->getPayerAccounts();

            $this->getPayeeAccounts();

            $this->checkPayerBalance();

            $this->updatePayment();
        } else {
            $this->getPaymentSubjects();

            $this->getPayerAccounts();

            $this->getPayeeAccounts();

            $this->checkPayerBalance();

            $this->createPayment();
        }

        if ($this->payment->status === 'completed') {
            $this->updatePayerBalance();
            $this->updatePayeeBalance();
        }

        return $this->payment;
    }

    private function createCustomExchangeRate()
    {
        if (isset($this->request['customExchangeRate'])) {
            $customRate = ExchangeRate::create([
                'from_currency_id' => $this->request['secondPaidCurrency'],
                'to_currency_id' => $this->request['billCurrency'],
                'coefficient' => $this->request['customExchangeRate'],
                'is_custom_rate' => true
            ]);

            $this->request['exchangeRate'] = $customRate->id;
        }
    }

    protected function setSubjects()
    {
        $this->payer = $this->payment->payer;
        $this->payee = $this->payment->payee;

//        $this->payerAccountInBillCurrency = $this->payment->payerAccountInBillCurrency;
//        $this->payeeAccountInBillCurrency = $this->payment->payeeAccountInBillCurrency;
    }

    protected function getPaymentSubjects()
    {
        $this->payer = $this->getSubject($this->request['payer_type'], $this->request['payer']);
        $this->payee = $this->getSubject($this->request['payee_type'], $this->request['payee']);
    }

//    protected function getAccounts()
//    {
//        $this->payerAccountInBillCurrency = $this->getSubjectAccount($this->payer, $this->request['billCurrency'));
//        $this->payeeAccountInBillCurrency = $this->getSubjectAccount($this->payee, $this->request['billCurrency'));
//
//        if ($this->request['paidAmountInSecondCurrency') > 0) {
//            $this->payerAccountInSecondCurrency = $this->getSubjectAccount($this->payer, $this->request['secondPaidCurrency'));
//            $this->payeeAccountInSecondCurrency = $this->getSubjectAccount($this->payee, $this->request['secondPaidCurrency'));
//        }
//    }

    protected function getPayerAccounts()
    {
        $this->getAccounts($this->payer,
            $this->payerAccountInBillCurrency,
            $this->payerAccountInSecondCurrency);
    }

    protected function getPayeeAccounts()
    {
        $this->getAccounts($this->payee,
            $this->payeeAccountInBillCurrency,
            $this->payeeAccountInSecondCurrency);
    }

    protected function getAccounts($owner, &$accountInBillCurrency, &$accountInSecondCurrency)
    {
        $accountInBillCurrency = $this->getSubjectAccount($owner, $this->request['billCurrency']);
        if (isset($this->request['paidAmountInSecondCurrency']) && $this->request['paidAmountInSecondCurrency'] > 0)
            $accountInSecondCurrency = $this->getSubjectAccount($owner, $this->request['secondPaidCurrency']);
    }

    protected function checkPayerBalance()
    {
        if ($this->payerAccountInBillCurrency) {
            $amount = isset($this->payment) ? $this->payment->paidAmountInBillCurrency
                : $this->request['paidAmountInBillCurrency'];
            if ($this->payerAccountInBillCurrency->balance - $amount < 0)
                abort(422,
                    'Недостаточно средств на балансе плательщика '
                    . $this->payerAccountInBillCurrency->balance
                    . ' ' . $this->payerAccountInBillCurrency->currency->isoName);
        }

        if ($this->payerAccountInSecondCurrency) {
            $amount = isset($this->payment) ? $this->payment->paidAmountInSecondCurrency
                : $this->request['paidAmountInSecondCurrency'];
            if ($this->payerAccountInSecondCurrency->balance - $amount < 0)
                abort(422,
                    'Недостаточно средств на балансе плательщика '
                    . $this->payerAccountInSecondCurrency->balance
                    . ' ' . $this->payerAccountInSecondCurrency->currency->isoName);
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

    /**
     * @param $subject
     * @param $currencyId
     * @return account for branch, in case of user returns null
     */
    protected function getSubjectAccount($subject, $currencyId)
    {
        $account = null;

        if ($subject instanceof Branch) {
            $account = $subject->accounts()->where('currency_id', $currencyId)->firstOrFail();
        }

        return $account;
    }

    protected function updatePayment()
    {
        $this->payment->status = $this->request['status'];
//        $this->payment->cashier_id = auth()->user()->id;
        $this->payment->branch_id = auth()->user()->branch->id;
        $this->payment->billAmount = $this->request['billAmount'];
        $this->payment->paidAmountInBillCurrency = $this->request['paidAmountInBillCurrency'];
        $this->payment->paidAmountInSecondCurrency = $this->request['paidAmountInSecondCurrency'];
        $this->payment->second_paid_currency_id = $this->request['secondPaidCurrency'];
        $this->payment->exchange_rate_id = $this->request['exchangeRate'];
        $this->payment->comment = $this->request['comment'];
        $this->payment->fillExtras();
        $this->payment->save();
    }

    protected function createPayment()
    {
        $this->payment = new Payment([
            'branch_id' => auth()->user()->branch->id,
//            'cashier_id' => auth()->user()->id,
            'status' => $this->request['status'],
//            'prepared_by_id' => $this->request['status'] === 'pending' ? auth()->user()->id : null,
            'payer_id' => $this->request['payer'],
            'payer_account_in_bill_currency_id' =>
                $this->payerAccountInBillCurrency === null ? null : $this->payerAccountInBillCurrency->id,
            'payer_account_in_second_currency_id' =>
                $this->payerAccountInSecondCurrency === null ? null : $this->payerAccountInSecondCurrency->id,
            'payer_type' => $this->request['payer_type'],
            'payee_id' => $this->request['payee'],
            'payee_account_in_bill_currency_id' =>
                $this->payeeAccountInBillCurrency === null ? null : $this->payeeAccountInBillCurrency->id,
            'payee_account_in_second_currency_id' =>
                $this->payeeAccountInSecondCurrency === null ? null : $this->payeeAccountInSecondCurrency->id,
            'payee_type' => $this->request['payee_type'],
            'payment_item_id' => $this->request['paymentItem'],
            'billAmount' => $this->request['billAmount'],
            'paidAmountInBillCurrency' => $this->request['paidAmountInBillCurrency'],
            'paidAmountInSecondCurrency' =>
                isset($this->request['paidAmountInSecondCurrency']) ? $this->request['paidAmountInSecondCurrency'] : 0,
            'bill_currency_id' => $this->request['billCurrency'],
            'second_paid_currency_id' =>
                isset($this->request['secondPaidCurrency']) ? $this->request['secondPaidCurrency'] : null,
            'exchange_rate_id' => isset($this->request['exchangeRate']) ? $this->request['exchangeRate'] : null,
            'comment' => $this->request['comment'],
        ]);

        $this->payment->fillExtras();
        $this->payment->save();
    }

    protected function updatePayerBalance()
    {
        if ($this->payerAccountInBillCurrency) {
            $this->payerAccountInBillCurrency->balance -= $this->payment->paidAmountInBillCurrency;
            $this->payerAccountInBillCurrency->save();
        }

        if ($this->payerAccountInSecondCurrency) {
            $this->payerAccountInSecondCurrency->balance -= $this->payment->paidAmountInSecondCurrency;
            $this->payerAccountInSecondCurrency->save();
        }
    }

    protected function updatePayeeBalance()
    {
        if ($this->payeeAccountInBillCurrency) {
            $this->payeeAccountInBillCurrency->balance += $this->payment->paidAmountInBillCurrency;
            $this->payeeAccountInBillCurrency->save();
        }

        if ($this->payeeAccountInSecondCurrency) {
            $this->payeeAccountInSecondCurrency->balance += $this->payment->paidAmountInSecondCurrency;
            $this->payeeAccountInSecondCurrency->save();
        }
    }
}
