@extends('layouts.app')

@section('content')
    <incoming-payment-editor :payment="{{$payment}}" disable></incoming-payment-editor>
@endsection
