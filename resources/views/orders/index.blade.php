@extends('layouts.app')

@section('content')
    <div class="container">
        <orders-table action='orders/all' :branches="{{$branches}}"></orders-table>
    </div>
@endsection

