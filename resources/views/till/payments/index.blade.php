@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <payments-table :branches="{{$branches}}"></payments-table>
        </div>
    </div>
@endsection
