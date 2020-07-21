@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <customs-report-editor class="col-12" :stored-items="{{$storedItems}}" :customs-codes="{{$customCodes}}"></customs-report-editor>
        </div>
    </div>
@endsection
