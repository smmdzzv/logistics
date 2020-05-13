<?php


namespace App\Data\Reports;


use App\Models\Currency;
use App\Models\SerializableModel;

class CashReport extends SerializableModel
{
    private $cashArray = [];

    private $branch;

    private $query;

    public function __construct($query, $branchId)
    {
        $this->query = $query;
        $this->branch = $branchId;
    }

    public function formReport()
    {
        $this->query->chunk(200, function ($payments) {
            foreach ($payments as $payment) {

                if ($payment->payee_type === 'branch' && $payment->payee_id === $this->branch) {
                    $this->cashArray[$payment->bill_currency_id] =
                        $this->getBillAmount($payment) + $payment->paidAmountInBillCurrency;
                    if ($payment->second_paid_currency_id && $payment->paidAmountInSecondCurrency)
                        $this->cashArray[$payment->second_paid_currency_id] =
                            $this->secondAmount($payment) + $payment->paidAmountInSecondCurrency;
                }
                if ($payment->payer_type === 'branch' && $payment->payer_id === $this->branch) {
                    $this->cashArray[$payment->bill_currency_id] =
                        $this->getBillAmount($payment) - $payment->paidAmountInBillCurrency;
                    if ($payment->second_paid_currency_id && $payment->paidAmountInSecondCurrency)
                        $this->cashArray[$payment->second_paid_currency_id] =
                            $this->secondAmount($payment) - $payment->paidAmountInSecondCurrency;
                }
            }
        });
    }

    private function getBillAmount($payment)
    {
        return isset($this->cashArray[$payment->bill_currency_id]) ? $this->cashArray[$payment->bill_currency_id] : 0;
    }

    private function secondAmount($payment)
    {
        return isset($this->cashArray[$payment->second_paid_currency_id]) ? $this->cashArray[$payment->second_paid_currency_id] : 0;
    }

    public function convertToISONameKey()
    {
        $currencies = Currency::all();
        $newArray = [];
        foreach ($this->cashArray as $key => $value) {
            $newArray[$currencies->firstWhere('id', $key)->isoName] = $value;
        }

        $this->cashArray = $newArray;
    }

    public function toArray()
    {
        return $this->cashArray;
    }

}
