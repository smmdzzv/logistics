@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="card shadow">
                    <div class="card-header">Выдать товары клиенту</div>
                    <div class="card-body">
                        <order-items-list-editor hover selectable
                                                 @if($orderPayment) :order-payment="{{$orderPayment}}" @endif
                        ></order-items-list-editor>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
