@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header">Оформить заказ</div>
                <div class="card-body">
                    <order-editor :user="{{$user}}" :tariffs="{{$tariffs}}"></order-editor>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
