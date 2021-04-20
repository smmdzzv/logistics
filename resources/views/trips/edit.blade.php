@extends('layouts.app')

@section('content')
    <trips-editor :cars="{{$cars}}" :trip="{{$trip}}" :branches="{{$branches}}" :car-providers="{{$carProviders}}" is-edit-mode></trips-editor>
@endsection
