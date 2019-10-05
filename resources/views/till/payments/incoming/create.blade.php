@extends('layouts.app')

@section('content')
    <incoming-payment-editor :currencies="{{$currencies}}" :account-to="{{$accountTo}}"></incoming-payment-editor>
@endsection
