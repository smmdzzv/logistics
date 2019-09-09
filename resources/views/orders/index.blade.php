@extends('layouts.app')

@section('content')
<orders-table :branches="{{$branches}}"></orders-table>
@endsection

