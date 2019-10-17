<?php

namespace App\Data\RequestWriters\Payments;

use App\Data\RequestWriters\RequestWriter;
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
        $filter = ['orderId', 'accountTo'];
        $data = array_filter($this->input->payment, function ($key) use ($filter) {
            return !in_array($key, $filter);
        }, ARRAY_FILTER_USE_KEY);
        $this->saved->payment = Payment::create($data);
    }
}
