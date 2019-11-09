@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row ">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        Список потерянных товаров
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <a class="btn btn-primary ml-auto mr-3" href="{{route('lost-items.create')}}">Добавить</a>
                        </div>

                        <div class="row justify-content-center bg-light py-3 my-3">
                            <div class="col-md-4">
                                Код товары
                            </div>
                            <div class="col-md-2">
                                Скидка, USD
                            </div>
                            <div class="col-md-2">
                                Кол-во мест
                            </div>
                            <div class="col-md-4">
                                Дата создания
                            </div>
                        </div>

                        @foreach($lostItems as $item)
                            <div class="row justify-content-center my-3">
                                <div class="col-md-4">
                                    {{$item->storedItem->code}}
                                </div>
                                <div class="col-md-2">
                                    {{$item->discount}}
                                </div>
                                <div class="col-md-2">
                                    {{$item->placeCount}}
                                </div>
                                <div class="col-md-4">
                                    {{$item->created_at}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-4">
                    {{ $lostItems->links() }}
                </div>

            </div>
        </div>
    </div>
@endsection

