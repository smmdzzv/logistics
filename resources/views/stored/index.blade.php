@extends('layouts.app')

@section('content')
    <div class="container col-12">
        <stored-table class="shadow" flowable excel-export :branches="{{$branches}}"></stored-table>
    </div>
    <div class="container col-12">
        <stored-item-info-table class="shadow" flowable excel-export url="stored-item-info/filtered?" :branches="{{$branches}}"></stored-item-info-table>
    </div>
@endsection

