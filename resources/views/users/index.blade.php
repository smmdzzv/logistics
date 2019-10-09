@extends('layouts.app')

@section('content')
    <users-table title="{{$title}}"
                 url={{$url}}
                 @if(isset($roles)) :roles="{{$roles}}" @endif/>
@endsection
