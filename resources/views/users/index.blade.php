@extends('layouts.app')

@section('content')
    <users-table :users="{{$users}}" type="{{Request::path()}}"></users-table>
@endsection
