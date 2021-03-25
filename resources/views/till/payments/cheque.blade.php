<div class="bg-white">
    <table style="width:100%">
        <tr>
            <td>№<strong>{{$payment->number}}</strong> Платеж от <strong>
                    <span
                        v-luxon="{ value: '{{$payment->updated_at}}' }"/></strong></td>
            <td>Кассир <strong>{{$payment->creator->name}}</strong></td>
            <td>{{$title}}</td>
        </tr>
        <tr>
            <td>
                @if($payment->payer)
                    @if($payment->payer_type === 'user')
                        Код клиента <strong>{{$payment->payer->code}}</strong>
                    @elseif($payment->payer_type === 'branch')
                        Филиал <strong>{{$payment->payer->name}}</strong>
                    @endif
                @endif
            </td>
            <td>
                @if($payment->payee)
                    @if($payment->payee_type === 'user')
                        Код клиента <strong>{{$payment->payee->code}}</strong>
                    @elseif($payment->payee_type === 'branch')
                        Филиал <strong>{{$payment->payee->name}}</strong>
                    @endif
                @endif
            </td>

            <td> Статья: <strong>{{$payment->paymentItem->title}}</strong></td>
        </tr>
        <tr>
            <td>
                К олплате <strong>{{$payment->billAmount}} {{$payment->billCurrency->isoName}}</strong>
                (<span class="number-as-string"
                       style="font-style: italic;text-decoration: underline;"></span>)
            </td>

            <td> В сомони: {{$payment->billAmountInTjs}} TJS</td>
            <td>
                Оплачено <strong>{{$payment->paidAmountInBillCurrency}} {{$payment->billCurrency->isoName}}</strong>

                @if($payment->paidAmountInSecondCurrency > 0)
                    +
                    <strong>{{$payment->paidAmountInSecondCurrency}} {{$payment->secondPaidCurrency->isoName}}</strong>
                @endif
                @if($payment->exchangeRate)
                    Курс: <strong>{{$payment->exchangeRate->coefficient}}</strong>
                @endif
            </td>
        </tr>
        @if($payment->comment)
            <tr>
                <td colspan="3">Пояснение: {{$payment->comment}}</td>
            </tr>
        @endif
        <tr>
            <td>
                @if($payment->clientItemsSelection && $payment->clientItemsSelection->storedItems->count() > 0)
                    Оплаченные места: <strong>{{$payment->clientItemsSelection->storedItems->count()}}</strong>
                @endif
            </td>
            @if($payment->payer_type === 'user')
                <td>Остаток: <b>{{$payment->placesLeft}}</b></td>
                <td>Долг: <b>{{$payment->clientDebt}}</b></td>
            @endif
            <td></td>
            <td rowspan="3">
                <qrcode value="{{route('payment.show',$payment->id )}}" :options="{ scale:2 }"></qrcode>
            </td>
        </tr>
        <tr>
            <td colspan="2"> Подпись менеджера ________________________
        </tr>
        <tr>
            <td colspan="2"> Подпись клиента ________________________
        </tr>
    </table>
</div>
