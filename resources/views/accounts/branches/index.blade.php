@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Счета Дуоб</div>
                <div class="card-body">
                    <duob-accounts-viewer :branches="{{$branches}}"></duob-accounts-viewer>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
