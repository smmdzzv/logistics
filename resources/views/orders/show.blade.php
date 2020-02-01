@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">
        <div class="col-12 px-md-5 mb-5">
            <div class="card shadow">
                <div class="card-header">Детали заказа</div>
                <div class="card-body">
                    <order-viewer :order="{{$order}}"></order-viewer>
                </div>
            </div>
        </div>
    </div>

    @if(count($order->orderRemovedItems) > 0)
        <div class="row justify-content-center">
            <div class="col-12 px-md-5">
                <div class="card shadow">
                    <div class="card-header">Удаленные позиции</div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">Тип</th>
                            <th scope="col">Кол-во</th>
                            <th scope="col">Вес</th>
                            <th scope="col">Объем, м <sup>3</sup></th>
                            <th scope="col">Удалил</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order->orderRemovedItems as $removed)
                            <tr>
                                <td>{{$removed->storedItemInfo->item->name}}</td>
                                <td>{{$removed->storedItemInfo->count}}</td>
                                <td>{{$removed->storedItemInfo->weight}}</td>
                                <td>{{round($removed->storedItemInfo->length * $removed->storedItemInfo->height * $removed->storedItemInfo->width,2)}}</td>
                                <td>{{$removed->storedItemInfo->deletedBy->name}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection
