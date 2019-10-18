@extends('layouts.app')

@section('content')
    <div class="container">
        <stored-table class="shadow" flowable excel-export :branches="{{$branches}}"></stored-table>
    </div>
@endsection

