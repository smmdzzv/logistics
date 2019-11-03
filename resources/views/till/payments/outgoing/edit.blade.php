@extends('layouts.app')

@section('content')
    <outgoing-payment-editor :payment="{{$payment}}" disable></outgoing-payment-editor>
@endsection
