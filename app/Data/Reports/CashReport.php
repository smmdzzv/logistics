<?php


namespace App\Data\Reports;


use App\Models\Currency;
use App\Models\SerializableModel;

class CashReport extends SerializableModel
{
    private $cashArray = [];

    public function formReport($query)
    {
        $query->chunk(200, function ($payments) {
            foreach ($payments as $payment) {

                $billAmount = isset($this->cashArray[$payment->bill_currency_id]) ? $this->cashArray[$payment->bill_currency_id] : 0;
                $secondAmount = isset($this->cashArray[$payment->second_paid_currency_id]) ? $this->cashArray[$payment->second_paid_currency_id] : 0;

                if ($payment->payee_type === 'branch') {
                    $this->cashArray[$payment->bill_currency_id] = $billAmount + $payment->paidAmountInBillCurrency;
                    if ($payment->second_paid_currency_id && $payment->paidAmountInSecondCurrency)
                        $this->cashArray[$payment->second_paid_currency_id] = $secondAmount + $payment->paidAmountInSecondCurrency;
                }
                if ($payment->payer_type === 'branch') {
                    $this->cashArray[$payment->bill_currency_id] = $billAmount - $payment->paidAmountInBillCurrency;
                    if ($payment->second_paid_currency_id && $payment->paidAmountInSecondCurrency)
                        $this->cashArray[$payment->second_paid_currency_id] = $secondAmount - $payment->paidAmountInSecondCurrency;
                }
            }
        });
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
