@extends('layouts.app')

@section('content')
    <tariff-editor :data="{{$tariffs}}" :branches="{{$branches}}"></tariff-editor>
@endsection

