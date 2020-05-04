<?php


namespace App\Data\RequestWriters\Payments;


use App\Models\Till\PaymentItem;

class PaymentRequestWriterFabric
{
    private PaymentItem $paymentItem;

    private PaymentRequestWriter $writer;

    private array $request;

    public function __construct(array $request)
    {
        $this->paymentItem = PaymentItem::find($request['paymentItem']);
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
            case 'Обмен валют':
                $this->writer = new MoneyExchangePaymentRequestWriter($this->request);
                break;
            default:
                $this->writer = new PaymentRequestWriter($this->request);
                break;
        }

        return $this->writer;
    }
}
