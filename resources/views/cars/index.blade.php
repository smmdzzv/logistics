@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="md-6">Список машин</div>
            </div>
            <div class="card-body">
                <cars-table :cars="{{$cars}}"></cars-table>
            </div>
        </div>
    </div>
@endsection
