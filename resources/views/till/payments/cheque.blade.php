<div class="bg-white">
    <table style="width:100%">
        <tr>
            <td>Платеж от <strong><span v-luxon="{ value: '{{$payment->updated_at}}' }"/></strong></td>
            <td>Кассир <strong>{{$payment->cashier->name}}</strong></td>
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
        </tr>
        <tr>
            <td>
                Сумма к олплате <strong>{{$payment->billAmount}} {{$payment->billCurrency->isoName}}</strong>
            </td>
            <td>
                Оплачено <strong>{{$payment->paidAmountInBillCurrency}} {{$payment->billCurrency->isoName}}</strong>

                @if($payment->paidAmountInSecondCurrency > 0)
                    +
                    <strong>{{$payment->paidAmountInSecondCurrency}} {{$payment->secondPaidCurrency->isoName}}</strong>
                @endif
            </td>
            <td>
                @if($payment->exchangeRate)
                    Курс конвертации: <strong>{{$payment->exchangeRate->coefficient}}</strong>
                @endif
            </td>
        </tr>
        <tr>
            <td> Сумма прописью: <span class="number-as-string"
                                       style="font-style: italic;text-decoration: underline;"></span></td>
            <td> Статья: <strong>{{$payment->paymentItem->title}}</strong></td>
        </tr>
        @if($payment->comment)
            <tr>
                <td>Пояснение: {{$payment->comment}}</td>
            </tr>
        @endif
        <tr>
            <td>
                @if($payment->orderPaymentItems && $payment->orderPaymentItems->count() > 0)
                    Количество оплаченных мест: <strong>{{$payment->orderPaymentItems->count()}}</strong>
                @endif
            </td>
            <td></td>
            <td rowspan="3">
                <qr-code value="{{route('payment.show',$payment->id )}}" :options="{ width: 30, tag: 'img' }"></qr-code>
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
