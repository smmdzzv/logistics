@extends('layouts.app')

@section('content')
    <trip-items-list-editor :trip="{{$trip}}":branches="{{$branches}}"></trip-items-list-editor>
@endsection
