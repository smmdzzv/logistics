@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <scanner-index :branch="{{Auth::user()->branch}}"></scanner-index>
        </div>
    </div>
@endsection
