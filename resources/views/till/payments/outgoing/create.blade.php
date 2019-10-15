@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <outgoing-payment-editor :currencies="{{$currencies}}"  :payment-items="{{$paymentItems}}":account-from="{{$accountFrom}}"></outgoing-payment-editor>
        </div>
    </div>
@endsection
