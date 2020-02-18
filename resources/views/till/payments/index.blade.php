@extends('layouts.app')

@section('content')
    <div class="container col-12">
        <div class="row justify-content-center">
            <payments-table table-height="65vh"
                            :branches="{{$branches}}"
                            :currencies="{{$currencies}}"
                            :payment-items="{{$paymentItems}}"
                            flowable></payments-table>
        </div>
    </div>
@endsection
