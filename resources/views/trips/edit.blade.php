@extends('layouts.app')

@section('content')
    <trips-editor :cars="{{$cars}}" :trip="{{$trip}}" :branches="{{$branches}}" is-edit-mode></trips-editor>
@endsection
