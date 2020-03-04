@extends('layouts.app')

@section('content')
    <div class="container col-12">
        <orders-table action='orders/all' flowable :branches="{{$branches}}"></orders-table>
    </div>
@endsection

