@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Счета Дуоб</div>
                    <div class="card-body">
                        @foreach($branches as $branch)
                            <div class="alert alert-secondary">
                                <h5>{{$branch->name}}</h5>
                                @foreach($branch->accounts as $account)
                                    <span>{{$account->balance}} {{$account->currency->isoName}} | </span>
                                @endforeach
                            </div>
                            <hr>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
