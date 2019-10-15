@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row py-3">
            <div class="text-right pl-5"> {{$account->description}} | Баланс: {{$account->balance}} {{$account->currency->isoName}}</div>
        </div>
        <div class="row justify-content-center">

            <payments-table :branches="{{$branches}}"></payments-table>
        </div>
    </div>
@endsection
