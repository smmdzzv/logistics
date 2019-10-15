@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row ">
            <div class="col-md-10  text-right">Баланс: {{$account->balance}}</div>
        </div>
        <div class="row justify-content-center">
            <payments-table :branches="{{$branches}}"></payments-table>
        </div>
    </div>
@endsection
