@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    <ul>
                        <li><a href="/settings">Settings</a></li>
                        <li><a href="/order/create">Create Order</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
