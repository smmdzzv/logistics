@extends('layouts.app')

@section('content')
    <trips-editor :cars="{{$cars}}" :branches="{{$branches}}" :car-providers="{{$carProviders}}"></trips-editor>
@endsection
