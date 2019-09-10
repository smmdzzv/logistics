@extends('layouts.app')

@section('content')
    <profile :user="{{$user}}"></profile>
@endsection
{{--<script>--}}
{{--    import Profile from "../../js/components/users/Profile";--}}
{{--    export default {--}}
{{--        components: {Profile}--}}
{{--    }--}}
{{--</script>--}}
