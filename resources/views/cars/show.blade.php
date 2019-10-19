@extends('layouts.app')

@section('content')
    <div class="container col-lg-8 col-xl-6">
        <div class="card">
            <div class="card-header">
                <div class="md-6">Обзор машины</div>
            </div>
            {{--            <form id="deleteCar" name="deleteCar" method="post" action="{{route('cars.destroy', $car)}}">--}}
            {{--                @csrf--}}
            {{--                @method('delete')--}}
            {{--            </form>--}}
            <div class="card-body">
                <div class="row">
                    <h5 class="col-12 col-sm-6">Номер машины: {{$car->number}}</h5>
                    <div class="text-left text-sm-right col-12 col-sm-6 align-items-baseline">
                        <a href="{{route('cars.edit', $car)}}" class="pr-md-0 pr-lg-4">Редактировать</a>
                        <button class="btn btn-link" onclick="destroyCar()">Удалить</button>
                    </div>
                    <div class="jumbotron col-10 offset-1 mt-4">
                        <p>Серийный номер: {{$car->serial}}</p>
                        <p>Длина: {{$car->length}} | Ширина: {{$car->width}} | Высота: {{$car->height}} </p>
                        <p>Грузподъемность: {{$car->maxWeight}} кг | Общий объем: {{$car->maxCubage}} м<sup>3</sup></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    async function destroyCar(car) {
        let confirm = await window.app.$bvModal.msgBoxConfirm(`Вы уверены что хотите удалить машину {{$car->number}}?`,
            {
                centered: true,
                okTitle: 'Да',
                cancelTitle: 'Отменить',
                footerClass: 'border-0',
                title: 'Подтверждение удаления'
            });

        if (confirm) {
            try {
                let action = `/cars/{{$car->id}}`;
                const response = await axios.delete(action);
                location = '{{route('cars.index')}}';
            } catch (e) {
                await window.app.$bvModal.msgBoxOk(`Не удалось удалить машину  {{$car->number}}. Перезагрузите страницу и попробуйте еще раз`, {
                    centered: true,
                    okTitle: 'Закрыть',
                    footerClass: 'border-0',
                    title: 'Сообщение об ошибке'
                });
            }
        }
    }
</script>
