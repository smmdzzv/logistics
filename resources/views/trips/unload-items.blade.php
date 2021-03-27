@extends('layouts.app')

@section('content')
{{--    <div class="container">--}}
{{--        <unloaded-items-editor :trip="{{$trip}}"--}}
{{--                               :stored-items="{{$trip->loadedItems}}"--}}
{{--                               :branches="{{$branches}}"--}}
{{--                               selectable--}}
{{--                               title="Загрузить товары"--}}
{{--                               hover/>--}}
{{--    </div>--}}

<div class="container-fluid">
    <trip-items-editor :trip="{{$trip}}" :branches="{{$branches}}" action="unload"></trip-items-editor>
</div>
@endsection

