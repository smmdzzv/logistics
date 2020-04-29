@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Оформить заказ</div>
                <div class="card-body">
                    <order-editor :user="{{$user}}" :tariffs="{{$tariffs}}" :order="{{$order}}"></order-editor>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
