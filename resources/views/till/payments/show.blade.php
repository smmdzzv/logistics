@extends('layouts.app')

@section('content')
    <div class="container col-12">
        <div class="card">
            <div class="card-header">
                Платеж от {{$payment->updated_at}}
            </div>
            <div class="card-body">
                <h4>Общая информация</h4>
                <div class="jumbotron">
                    <p>Требуемая сумма: <b>{{$payment->billAmount}} {{$payment->billCurrency->isoName}}</b></p>
                    <p>Оплаченная сумма: <b>{{$payment->paidAmount}} {{$payment->paidCurrency->isoName}}</b></p>
                    @if($payment->exchangeRate)
                        <p>Обменный курс: <b>{{$payment->exchangeRate->fromCurrency->isoName}} в {{$payment->exchangeRate->toCurrency->isoName}} {{$payment->exchangeRate->coefficient}}</b></p>
                    @endif
                    @if($payment->payer)
                        <p>
                            Плательщик: <b>{{$payment->payer->name}}</b>
                            @if($payment->payerAccount)
                            &ndash; {{$payment->payerAccount->description}}
                            @endif
                        </p>
                    @endif

                    @if($payment->payee)
                        <p>
                            Получатель: <b>{{$payment->payee->name}}</b>
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

