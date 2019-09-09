@extends('layouts.app')

@section('content')
<stored-table :branches="{{$branches}}"></stored-table>
@endsection

