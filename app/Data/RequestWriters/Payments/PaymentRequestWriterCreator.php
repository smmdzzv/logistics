<?php


namespace App\Data\RequestWriters\Payments;


use App\Http\Requests\Till\PaymentRequest;
use App\Models\Till\PaymentItem;

class PaymentRequestWriterCreator
{
    private PaymentItem $paymentItem;

    private PaymentRequestWriter $writer;

    private PaymentRequest $request;

    public function __construct(PaymentRequest $request)
    {
        $this->paymentItem = PaymentItem::find($request->get('paymentItem'));
        $this->request = $request;
    }

    public function getWriter()
    {
        switch ($this->paymentItem->title) {
            case 'Пополнение баланса':
                $this->writer = new BalanceReplenishmentPaymentRequestWriter($this->request);
                break;
            case 'Перевод между счетами филиала':
                $this->writer = new TransferBetweenBranchAccountsRequestWriter($this->request);
                break;
            default:
                $this->writer = new PaymentRequestWriter($this->request);
                break;
        }

        return $this->writer;
    }
}
