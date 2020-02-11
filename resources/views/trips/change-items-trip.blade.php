@extends('layouts.app')

@section('content')
    
{{--        <trip-exchange-items-editor :trip="{{$trip}}"--}}
{{--                                    :stored-items="{{$trip->loadedItems}}"--}}
{{--                                    :trips="{{$trips}}"--}}
{{--                                    selectable--}}
{{--                                    hover>--}}
{{--        </trip-exchange-items-editor>--}}
        <div class="container">
            <trip-items-editor :trip="{{$trip}}" :trips="{{$trips}}" action="transfer"></trip-items-editor>
        </div>

@endsection
