@extends('layouts.app')

@section('content')
    <trip-items-editor :trip="{{$trip}}":branches="{{$branches}}"></trip-items-editor>
@endsection
