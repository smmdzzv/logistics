@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header">Обменять валюту</div>

                    <div class="card-body">
                        <money-exchanger :currencies="{{$currencies}}"></money-exchanger>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
