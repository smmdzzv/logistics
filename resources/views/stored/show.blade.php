@extends('layouts.app')

@section('content')
    <div class="container col-10">
        <div class="card">
            <div class="card-header">
                Информация о товаре <b>{{$storedItem->code}}</b>
            </div>
            <div class="card-body">
                <h4>Общая информация</h4>
                <div class="jumbotron">
                    <p>Владелец: <b>{{$storedItem->info->owner->code}} {{$storedItem->info->owner->name}}</b></p>
                    <p>Количество мест: <b>{{$storedItem->info->placeCount}}</b>
                        | Стоимость: <b>{{$storedItem->info->billingInfo->pricePerItem}} USD</b></p>
                </div>
                <h4>Габариты</h4>
                <div class="jumbotron">
                    <p>Вес: {{$storedItem->info->weight}} кг</p>
                    <p>ШxВxД: {{$storedItem->info->width}} x {{$storedItem->info->length}}
                        x {{$storedItem->info->height}} м</p>
                    <p>
                        Объем: {{round($storedItem->info->weight * $storedItem->info->length * $storedItem->info->height, 2)}}
                        м<sup>3</sup></p>
                </div>
                <h4>История хранения</h4>
                <div class="jumbotron">
                    @foreach($storageHistories as $history)
                        <p>Склад: <b>{{$history->storage->name}}</b></p>
                        <p>Дата приемки:<b> {{$history->created_at}}</b></p>
                        <p>Принял: <b>{{$history->registeredBy->code}} {{$history->registeredBy->name}}</b></p>
                        @if($history->deleted_at)
                            <p>Дата выдачи: <b>{{$history->deleted_at}}</b></p>
                            <p>Выдал: <b>{{$history->deletedBy->code}} {{$history->deletedBy->name}}</b></p>
                        @endif
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @endforeach
                </div>
                <h4>История перевозок</h4>
                <div class="jumbotron">
                    @foreach($tripHistories as $history)
                        <p>Рейс: <b>{{$history->trip->code}}</b></p>
                        <p>Добавлен в предварительный список: <b>{{$history->created_at}}</b></p>
                        <p>Добавил: <b>{{$history->registeredBy->code}} {{$history->registeredBy->name}} </b>
                        </p>
                        @if($history->loaded_at)
                            <p>Загружен на рейс: <b>{{$history->loaded_at}}</b></p>
                            <p>Загрузил: <b>{{$history->loadedBy->code}} {{$history->loadedBy->name}}</b></p>
                        @endif
                        @if($history->deleted_at)
                            <p>Дата снятия с рейса: <b>{{$history->deleted_at}}</b></p>
                            <p>Снял с рейса: <b>{{$history->deletedBy->code}} {{$history->deletedBy->name}}</b></p>
                        @endif
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

    </div>
@endsection

