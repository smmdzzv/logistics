@extends('layouts.app')

@section('content')
    <div class="container">
        <loaded-items-editor :trip="{{$trip}}"
                             :stored-items="{{$trip->unloadedItems}}"
                             selectable
                             title="Загрузить товары"
                             hover/>
    </div>
@endsection

