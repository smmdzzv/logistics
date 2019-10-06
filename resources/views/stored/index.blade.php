@extends('layouts.app')

@section('content')
    <div class="container">
        <stored-table class="shadow" :branches="{{$branches}}"></stored-table>
    </div>
@endsection

