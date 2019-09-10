@extends('layouts.app')

@section('content')
    <div class="container">
        <orders-table :action='order/all' :branches="{{$branches}}"></orders-table>
    </div>
@endsection

