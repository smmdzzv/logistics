@extends('layouts.app')

@section('content')
    <div class="container col-12">
        <div class="row justify-content-center">
            <payments-table table-height="65vh"
                            comment="{{$account->description}} | Баланс: {{$account->balance}} {{$account->currency->isoName}}"
                            :branches="{{$branches}}"
                            :currencies="{{$currencies}}"></payments-table>
        </div>
    </div>
@endsection
