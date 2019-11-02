@extends('layouts.app')

@section('content')
    <incoming-payment-editor :currencies="{{$currencies}}" :accounts-to="{{$accountsTo}}"></incoming-payment-editor>
@endsection
