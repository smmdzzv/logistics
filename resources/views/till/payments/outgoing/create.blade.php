@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <outgoing-payment-editor :currencies="{{$currencies}}"  :payment-items="{{$paymentItems}}":accounts-from="{{$accountsFrom}}"></outgoing-payment-editor>
        </div>
    </div>
@endsection
