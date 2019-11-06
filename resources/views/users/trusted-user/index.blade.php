@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header">
                    Доверенные пользователи
                </div>
                <div class="card-body">
                    <div class="row mb-4 mr-4">
                        <a class="ml-auto btn btn-primary"
                           href="{{route('trusted-user.create')}}">
                            Добавить
                        </a>
                    </div>

                    <div class="row bg-light py-3">
                        <div class="col-md-4">
                            Клиент
                        </div>
                        <div class="col-md-2">
                            Дата начала
                        </div>
                        <div class="col-md-2">
                            Дата конца
                        </div>
                        <div class="col-md-3">
                            Макс. допуст. долг
                        </div>
                        <div class="col-md-1">
                        </div>
                    </div>

                    @foreach($trustedUsers as $trusted)
                        <div class="row my-3 justify-content-center">
                            <div class="col-md-4 my-1 my-md-0">
                                {{$trusted->user->code}} {{$trusted->user->name}}
                            </div>
                            <div class="col-md-2 my-1 my-md-0">
                                {{$trusted->from}}
                            </div>
                            <div class="col-md-2 my-1 my-md-0">
                                {{$trusted->to}}
                            </div>
                            <div class="col-md-3 my-1 my-md-0">
                                {{$trusted->maxDebt}}
                            </div>
                            <div class="col-md-1 my-1 my-md-0">
                                <a href="#" onclick="deleteTrusted('{{$trusted->id}}')">
                                    <img class="icon-btn-sm" src="/svg/delete.svg">
                                </a>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

@endsection
<script>
    async function deleteTrusted(trustedId) {
        tShowSpinner();
        try {
            await axios.delete('/trusted-user/' + trustedId);
            window.location.reload();
        }
        catch(e){
            tHideSpinner();
        }
    }
</script>
