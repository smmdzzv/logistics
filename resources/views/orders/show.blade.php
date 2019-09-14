@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Детали заказа</div>
                    <div class="card-body">
                         <order-viewer :order="{{$order}}"></order-viewer>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection