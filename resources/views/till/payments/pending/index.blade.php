@extends('layouts.app')

@section('content')
    <div class="container col-12">
        <div class="row justify-content-center">
            <pending-payments-table table-height="65vh"
                                    :branches="{{$branches}}">
            </pending-payments-table>
        </div>
    </div>
@endsection
