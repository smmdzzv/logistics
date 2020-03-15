@extends('layouts.app')

@section('content')
    {{--    <div class="container col-12">--}}
    {{--        <stored-table class="shadow" flowable excel-export :branches="{{$branches}}"></stored-table>--}}
    {{--    </div>--}}
    <div class="container col-12">
        {{--        <stored-item-info-table class="shadow" flowable excel-export url="stored-item-info/filtered?" :columns-to-hide="['selectedCount']" :branches="{{$branches}}"></stored-item-info-table>--}}
        <filterable-stored-item-info-table class="shadow"
                                           flowable
                                           excel-export
                                           :columns-to-hide="['selectedCount']"
                                           :branches="{{$branches}}"
                                           :items="{{$items}}">
        </filterable-stored-item-info-table>
    </div>
@endsection
