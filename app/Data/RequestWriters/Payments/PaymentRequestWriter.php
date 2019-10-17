<?php

namespace App\Data\RequestWriters\Payments;

use App\Data\RequestWriters\RequestWriter;
use App\Models\LegalEntities\LegalEntity;
use App\Models\Till\Payment;

class PaymentRequestWriter extends RequestWriter
{

    function write()
    {
        return $this->writePayment();
    }

    private function writePayment()
    {
        $this->input->payment['branchId'] = $this->input->branchId;
        $this->input->payment['cashierId'] = $this->input->cashierId;

        if (!isset($this->input->payment['currencyId'])) {
            $this->data->duobAccount = LegalEntity::first()->accounts()->with('currency')->first();
            $this->input->payment['currencyId'] = $this->data->duobAccount->currency->id;
        }

        $filter = ['orderId', 'accountTo', 'accountFrom'];
        $data = array_filter($this->input->payment, function ($key) use ($filter) {
            return !in_array($key, $filter);
        }, ARRAY_FILTER_USE_KEY);
        $this->saved->payment = Payment::create($data);
    }
}
