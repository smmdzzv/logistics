@extends('layouts.app')

@section('content')
    <div class="container">
        <unloaded-items-editor :trip="{{$trip}}"
                               :stored-items="{{$trip->storedItems}}"
                               :branches="{{$branches}}"
                               selectable
                               title="Загрузить товары"
                               hover/>
    </div>
@endsection

