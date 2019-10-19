@extends('layouts.app')

@section('content')

        <div class="row justify-content-center">
            <div class="col-12 px-md-5">
                <div class="card shadow">
                    <div class="card-header">Детали заказа</div>
                    <div class="card-body">
                         <order-viewer :order="{{$order}}"></order-viewer>
                    </div>
                </div>
            </div>
        </div>

@endsection
