@if($showProfileLink)
    <div class="row pr-4">
        <p class="ml-auto "><a href="/payment/{{$payment->id}}">Перейти к платежу</a></p>
    </div>
@endif
<div class="jumbotron">
    @if($payment->clientItemsSelection
            && $payment->clientItemsSelection->storedItems->count() > 0
            && $payment->paymentItem->title === 'Пополнение баланса')
        <div class="row">
            <a class="ml-3 mb-3 btn btn-dark" href="/orders/items/edit?payment={{$payment->id}}">Выдать товары</a>
        </div>
    @endif
    <div class="row">
        <p class="col-md-4">Номер чека: <b>{{$payment->number}}</b></p>
        @if($payment->status !== 'completed')
            <a class="btn btn-link ml-auto" href="{{route('payment.edit', $payment->id)}}">Редактировать платеж</a>
        @endif
    </div>

    <p>Требуемая сумма: <b>{{$payment->billAmount}} {{$payment->billCurrency->isoName}}</b></p>
    <p>Требуемая сумма в сомони: <b>{{$payment->billAmountInTjs}} TJS</b></p>
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
    @if($payment->payer_type === 'user')
        <p>Долг: <b>{{$payment->clientDebt}} USD</b></p>
        <p>Остаток мест: <b>{{$payment->placesLeft}}</b></p>
    @endif
    <p>Комментарий: <b>{{$payment->comment}}</b></p>
    @if($payment->creator)
        <p>Платеж подготовил: <b>{{$payment->creator->name}}</b></p>
        <p>Дата создания платежа: <b><span v-luxon="{ value: '{{$payment->created_at}}'}"/></b></p>
    @endif
    <p>Операцию провел: <b>{{$payment->editor->name}}</b></p>
    <p>Дата изменения платежа: <b><span v-luxon="{ value: '{{$payment->updated_at}}'}"/></b></p>
    <p>Операция проведена в <b>{{$payment->branch->name}}</b></p>
</div>
