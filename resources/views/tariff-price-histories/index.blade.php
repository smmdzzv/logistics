@extends('layouts.app')

@section('content')
    <tariff-histories-viewer :histories="{{$histories}}"></tariff-histories-viewer>
@endsection

