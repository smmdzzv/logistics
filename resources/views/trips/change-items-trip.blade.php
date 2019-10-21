@extends('layouts.app')

@section('content')
    <div class="container">
        <trip-exchange-items-editor :trip="{{$trip}}"
                                    :stored-items="{{$trip->loadedItems}}"
                                    :trips="{{$trips}}"
                                    selectable
                                    hover>
        </trip-exchange-items-editor>
    </div>
@endsection
