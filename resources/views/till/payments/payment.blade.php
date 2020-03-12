@if($showProfileLink)
    <div class="row pr-4">
        <p class="ml-auto "></p><a href="/payments/{{$payment->id}}">Перейти к платежу</a></p>
    </div>
@endif
<div class="jumbotron">
    <p>Требуемая сумма: <b>{{$payment->billAmount}} {{$payment->billCurrency->isoName}}</b></p>
    <p>Оплаченная сумма:
        <b>{{$payment->paidAmountInBillCurrency}} {{$payment->billCurrency->isoName}}</b>
        @if($payment->paidAmountInSecondCurrency > 0 && $payment->secondPaidCurrency)
            <b>+ {{$payment->paidAmountInSecondCurrency}} {{$payment->secondPaidCurrency->isoName}}</b>
        @endif
    </p>
    @if($payment->exchangeRate)
        <p>Обменный курс: <b>{{$payment->exchangeRate->fromCurrency->isoName}}
                в {{$payment->exchangeRate->toCurrency->isoName}} {{$payment->exchangeRate->coefficient}}</b>
        </p>
    @endif
    @if($payment->payer)
        <p>
            Плательщик: <b>{{$payment->payer->code}} {{$payment->payer->name}}</b>
            @if($payment->payerAccount)
            &ndash; {{$payment->payerAccount->description}}
            @endif
        </p>
    @endif

    @if($payment->payee)
        <p>
            Получатель: <b>{{$payment->payee->code}} {{$payment->payee->name}}</b>
            @if($payment->payeeAccount)
            &ndash; {{$payment->payeeAccount->description}}
            @endif
        </p>
    @endif

    <p>Статья: <b>{{$payment->paymentItem->title}}</b></p>
    <p>Комментарий: <b>{{$payment->comment}}</b></p>
    @if($payment->preparedBy)
        <p>Заявку подготовил: <b>{{$payment->preparedBy->name}}</b></p>
        <p>Дата создания заявки: <b>{{$payment->created_at}}</b></p>
    @endif
    <p>Операцию провел: <b>{{$payment->cashier->name}}</b></p>
    <p>Операция проведена в <b>{{$payment->branch->name}}</b></p>
</div>
