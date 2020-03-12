@extends('layouts.app')

@section('content')
    <div class="p-5">
        <table style="width:100%" cellpadding="6">
            <tr>
                <td>Платеж от <strong><span v-luxon="{ value: '{{$payment->updated_at}}' }"/></strong></td>
                <td>Кассир <strong>{{$payment->cashier->name}}</strong></td>
                <td>Склад</td>
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
                <td colspan="3"> Сумма прописью: <span class="number-as-string"></span></td>
            </tr>
            <tr>
                <td>
                    @if($payment->orderPaymentItems && $payment->orderPaymentItems->count() > 0)
                        Количество оплаченных мест: <strong>{{$payment->orderPaymentItems->count()}}</strong>
                    @endif
                </td>
                <td></td>
                <td rowspan="3">
                    <qr-code value="{{route('payment.show',$payment->id )}}" :options="{ width: 120 }"></qr-code>
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
    <div class="container col-12">
        <div class="card">
            <div class="card-header">
                @if($payment->status === 'completed')
                    Платеж
                @else
                    Заявка
                @endif
                от <span v-luxon="{ value: '{{$payment->updated_at}}' }"/>
            </div>
            <div class="card-body">
                <h4>Общая информация</h4>
                @component('till/payments/payment', ['payment' => $payment, 'showProfileLink' => false])@endcomponent

                @if($payment->relatedPayment || count($payment->relatedPayments) >0)
                    <h4>Связанные платежи</h4>
                @endif
                @if($payment->relatedPayment)
                    @component('till/payments/payment', ['payment' => $payment->relatedPayment, 'showProfileLink' => true])@endcomponent
                @endif
                @foreach($payment->relatedPayments as $relatedPayment)
                    @component('till/payments/payment', ['payment' => $relatedPayment, 'showProfileLink' => true])@endcomponent
                @endforeach

                @if($payment->orderPaymentItems && $payment->orderPaymentItems->count() > 0)
                    <h4>Оплаченные товары</h4>
                    <div class=" ">
                        <div class="card">
                            <div class="card-header text-center">
                                <div class="form-row text-center">
                                    <div class="col-md-2 text-md-left">
                                        <span>Наименование</span>
                                    </div>
                                    <div class="col-md-3 text-md-left">
                                        <span>Код</span>
                                    </div>
                                    <div class="col-md-3 text-md-left">
                                        <span>Ш &times; В &times; Д</span>
                                    </div>
                                    <div class="col-md-1 text-md-left">
                                        <span>Вес</span>
                                    </div>
                                    <div class="col-md-1 text-md-left">
                                        <span>Цена за кг</span>
                                    </div>
                                    <div class="col-md-1 text-md-left">
                                        <span>Цена</span>
                                    </div>
                                    <div class="col-md-1">
                                    </div>
                                </div>
                            </div>
                            @foreach($payment->orderPaymentItems as $orderPaymentItem)
                                <li class="list-group-item">
                                    <div class="form-row text-center">
                                        <div class="col-md-2 text-md-left">
                                            <span>{{$orderPaymentItem->storedItem->info->item->name}}</span>
                                        </div>
                                        <div class="col-md-3 text-md-left">
                                            <span>{{$orderPaymentItem->storedItem->code}}</span>
                                        </div>
                                        <div class="col-md-3 text-md-left">
                                            <span>{{$orderPaymentItem->storedItem->info->width}}
                                                &times; {{$orderPaymentItem->storedItem->info->height}}
                                                &times; {{$orderPaymentItem->storedItem->info->length}}
                                            </span>
                                        </div>
                                        <div class="col-md-1 text-md-left">
                                            <span>{{$orderPaymentItem->storedItem->info->weight}} кг</span>
                                        </div>
                                        <div class="col-md-1 text-md-left">
                                            <span>{{round($orderPaymentItem->storedItem->info->billingInfo->pricePerItem / $orderPaymentItem->storedItem->info->weight, 2)}} $</span>
                                        </div>
                                        <div class="col-md-1 text-md-left">
                                            <span>{{$orderPaymentItem->storedItem->info->billingInfo->pricePerItem}} $</span>
                                        </div>
                                        <div class="col-md-1">
                                            <a href="{{route('stored.show', $orderPaymentItem->storedItem->id)}}">
                                                <img src="/svg/file.svg" class="icon-btn-sm">
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let arr = document.getElementsByClassName('number-as-string');
        let tines = {
            USD: ['цент', 'цента', 'центов'],
            RUB: ['копейка', 'копейки', 'копеек'],
            TJS: ['дирам', 'дирамы', 'дирамов']
        };

        let currencies = {
            USD: ['доллар', 'доллара', 'долларов'],
            RUB: ['рубль', 'рубля', 'рублей'],
            TJS: ['сомони', 'сомони', 'сомони']
        };

        for (let i = 0; i < arr.length; i++) {
            let number = {{$payment->billAmount}};
            let decimalPart = (number + '').split('.')[1];
            if (!decimalPart)
                decimalPart = 0;

            let str = numberToString(number)
                .replace('_', morph(Math.trunc(number), currencies['{{$payment->billCurrency->isoName}}']))
                .replace('?', morph(decimalPart, tines['{{$payment->billCurrency->isoName}}']));

            $(arr[i]).text(str);
        }
    });
</script>

