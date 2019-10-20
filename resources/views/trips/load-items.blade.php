@extends('layouts.app')

@section('content')
    <div class="container">
        <loaded-items-editor :trip="{{$trip}}"
                             :stored-items="{{$trip->storedItems}}"
                             selectable
                             title="Загрузить товары"
                             hover/>
    </div>
@endsection

