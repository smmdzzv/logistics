
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Настройки') }}</div>

                <div class="card-body">
                    <form action="/settings/branch" method="post">
                        @csrf

                        <div class="row form-group">
                            <h4 class="pl-3 col-9">Филиалы</h4>
                            <div class="row col-12 align-items-sm-center">
                                <div class="col-8">
                                    <label for="branch-name" class="col-md-4 col-form-label">Имя филиала</label>

                                    <input id="branch-name"
                                           type="text"
                                           class="form-control @error('branch-name') is-invalid @enderror"
                                           name="name"
                                           value="{{ old('name') }}"
                                           required autocomplete="branch-name" autofocus>

                                    @error('branch-name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                     </span>
                                    @enderror
                                </div>
                                <div class="col-4">
                                    <input class="btn btn-primary" type="submit" content="Добавить">
                                </div>
                            </div>
                            <div class="table col-10 pt-4">
                                @foreach($branches as $branch)
                                    <div class="d-table-row">{{$branch->name}}</div>
                                @endforeach
                            </div>
                        </div>
                    </form>

                    <form action="/settings/position" method="post">
                        @csrf

                        <div class="row form-group">
                            <h4 class="pl-3 col-9">Должности</h4>
                            <div class="row col-12 align-items-sm-center">
                                <div class="col-8">
                                    <label for="position-name" class="col-md-4 col-form-label">Название должности</label>

                                    <input id="position-name"
                                           type="text"
                                           class="form-control @error('position-name') is-invalid @enderror"
                                           name="name"
                                           value="{{ old('name') }}"
                                           required autocomplete="position-name" autofocus>

                                    @error('branch-name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                     </span>
                                    @enderror
                                </div>
                                <div class="col-4">
                                    <input class="btn btn-primary" type="submit" content="Добавить">
                                </div>
                            </div>
                            <div class="table col-10 pt-4">
                                @foreach($positions as $position)
                                    <div class="d-table-row">{{$position->name}}</div>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
