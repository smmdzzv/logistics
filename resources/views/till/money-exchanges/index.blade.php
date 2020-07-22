@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                История курсов валют
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Дата</th>
                        <th scope="col">Из</th>
                        <th scope="col">В</th>
                        <th scope="col">Курс</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($rates as $rate)
                        <tr>
                            <td><span v-luxon="{ value: '{{$rate->created_at}}'}"/></td>
                            <td>{{$rate->fromCurrency->name}}</td>
                            <td>{{$rate->toCurrency->name}}</td>
                            <td>{{$rate->coefficient}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $rates->links() }}
            </div>
        </div>
    </div>
@endsection
