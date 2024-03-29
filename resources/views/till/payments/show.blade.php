@extends('layouts.app')

@section('content')
    <div class="container col-12 no-print" id="paymentInfo">
        <div @if($payment->deleted_at)
             class="card bg-danger"
             @else
             class="card"
            @endif>

            @if($payment->status === 'completed')
                <div class="card-header bg-primary">
            @else
                @if($payment->approved === null)
                    <div class="card-header bg-warning">
                @elseif($payment->approved === false)
                    <div class="card-header bg-danger">
                @else
                    <div class="card-header bg-success">
                @endif
            @endif
                <div class="row">
                    <div class="ml-3 font-weight-bold">
                        @if($payment->status === 'completed')
                            Платеж
                        @else
                            Заявка
                            @if($payment->approved === null)
                                <span>рассматривается</span>
                            @elseif($payment->approved === false)
                                <span>отклонена</span>
                            @else
                                <span>одобрена</span>
                            @endif
                        @endif
                        от <span v-luxon="{ value: '{{$payment->updated_at}}' }"/>
                    </div>
                    <div class="ml-auto mr-3">
                        <a class="btn btn-light" href="{{route('payment.index')}}">Перейти к списку платежей</a>
                    </div>
                    <div class="mr-3">
                        <button class="btn btn-secondary" onclick="printContent()">
                            Распечатать чек
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <h4>Общая информация</h4>
                @if($payment->deleted_at)
                    <h4>Удален сотрудником {{$payment->destroyer->code}} <span
                            v-luxon="{ value: '{{$payment->deleted_at}}'}"/></h4>
                @endif
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

                @if($payment->clientItemsSelection && $payment->clientItemsSelection->storedItems->count() > 0)
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
                            @foreach($payment->clientItemsSelection->storedItems as $storedItem)
                                <li class="list-group-item">
                                    <div class="form-row text-center">
                                        <div class="col-md-2 text-md-left">
                                            <span>{{$storedItem->info->item->name}}</span>
                                        </div>
                                        <div class="col-md-3 text-md-left">
                                            <span>{{$storedItem->code}}</span>
                                        </div>
                                        <div class="col-md-3 text-md-left">
                                            <span>{{$storedItem->info->width}}
                                                &times; {{$storedItem->info->height}}
                                                &times; {{$storedItem->info->length}}
                                            </span>
                                        </div>
                                        <div class="col-md-1 text-md-left">
                                            <span>{{$storedItem->info->weight}} кг</span>
                                        </div>
                                        <div class="col-md-1 text-md-left">
                                            <span>{{round($storedItem->info->billingInfo->pricePerItem / $storedItem->info->weight, 2)}} $</span>
                                        </div>
                                        <div class="col-md-1 text-md-left">
                                            <span>{{$storedItem->info->billingInfo->pricePerItem}} $</span>
                                        </div>
                                        <div class="col-md-1">
                                            <a href="{{route('stored-items.show', $storedItem->id)}}">
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


    <div class="d-none" id="cheques">
        @component('till/payments/cheque', ['payment' => $payment, 'title' => "Касса"])@endcomponent
        <hr>
        @component('till/payments/cheque', ['payment' => $payment, 'title' => "Склад"])@endcomponent
        <hr>
        @component('till/payments/cheque', ['payment' => $payment, 'title' => "Клиент"])@endcomponent
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

    function printContent() {
        $('#paymentInfo').addClass('no-print');
        $('footer').addClass('no-print');
        $('#cheques').removeClass('d-none');
        window.print();
        $('#cheques').addClass('d-none');
        $('#paymentInfo').removeClass('no-print');
        $('footer').removeClass('no-print');

    }
</script>

<style media="print">
    .no-print, .no-print * {
        display: none !important;
    }
</style>
