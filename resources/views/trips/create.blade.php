@extends('layouts.app')

@section('content')
        <trips-editor :cars="{{$cars}}"></trips-editor>
@endsection
