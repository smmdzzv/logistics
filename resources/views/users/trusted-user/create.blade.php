@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="col-md-11 col-lg-10 mx-auto">
            <div class="card">
                <div class="card-header">
                    Добавить доверенного пользователя
                </div>
                <div class="card-body">
                    <trusted-user-editor></trusted-user-editor>
                </div>
            </div>
        </div>
    </div>

@endsection

