@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">
            <div class="card mx-auto">
                <div class="card-header">
                    История изменений
                </div>
                <table role="table" class="table table-responsive">
                    <thead role="rowgroup">
                    <tr role="row">
                        <th role="columnheader">
                            Операция
                        </th>
                        <th role="columnheader">
                            Сотрудник
                        </th>
                        <th role="columnheader">
                            Дата
                        </th>
                    </tr>
                    </thead>

                    <tbody role="rowgroup">
                    @foreach($histories as $history)
                        <tr role="row">
                            <td role="cell">
                                @if($history->operation === 'store')
                                    Приемка на склад
                                @endif
                            </td>
                            <td role="cell">
                                {{$history->creator->code}} {{$history->creator->name}}
                            </td>
                            <td role="cell">
                                <span v-luxon="{ value: '{{$history->created_at}}' }"/>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="card-footer">
                    {{ $histories->links() }}
                </div>
            </div>
        </div>
    </div>


@endsection
