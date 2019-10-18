@extends('layouts.app')

@section('content')
    <div class="container col-12">
        <stored-table class="shadow" flowable excel-export :branches="{{$branches}}"></stored-table>
    </div>
@endsection

