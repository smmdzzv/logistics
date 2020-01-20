@extends('layouts.app')

@section('content')
    <trips-editor :cars="{{$cars}}" :branches="{{$branches}}"></trips-editor>
@endsection
