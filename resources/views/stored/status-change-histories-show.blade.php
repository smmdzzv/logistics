@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="card mx-auto">
                <div class="card-header">
                    История изменений от <span v-luxon="{ value: '{{$history->created_at}}' }"/>
                </div>
                <div class="card-body">
                    <p>Сотрудник: {{$history->creator->code}} {{$history->creator->name}}</p>
                    <table role="table" class="table table-responsive">
                        <thead role="rowgroup">
                        <tr role="row">
                            <th role="columnheader">
                                Код
                            </th>
                            <th role="columnheader">
                            </th>
                        </tr>
                        </thead>

                        <tbody role="rowgroup">
                        @foreach($history->storedItems as $storedItem)
                            <tr role="row">
                                <td role="cell">
                                    {{$storedItem->code}}
                                </td>
                                <td>
                                    <a href="{{route('stored-items.show', $storedItem->id)}}">
                                        <img src="/svg/file.svg" class="icon-btn-sm">
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection
