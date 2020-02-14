@extends('layouts.app')

@section('content')
    <payment-editor class="col-md-12 col-lg-10"
                    :branches="{{$branches}}"
                    :currencies="{{$currencies}}"
                    :payment-items="{{$paymentItems}}">
    </payment-editor>
@endsection
