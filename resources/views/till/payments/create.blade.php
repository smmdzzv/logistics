@extends('layouts.app')

@section('content')
    <payment-editor :currencies="{{$currencies}}" :account-to="{{$accountTo}}"></payment-editor>
@endsection
