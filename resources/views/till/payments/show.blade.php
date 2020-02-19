@extends('layouts.app')

@section('content')
    <div class="container col-12">
        <div class="card">
            <div class="card-header">
                @if($payment->status === 'completed')
                    Платеж
                @else
                    Заявка
                @endif
                от {{$payment->updated_at}}
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

