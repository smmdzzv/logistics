@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header">
                    Таможенные коды
                </div>
                <div class="card-body">
                    <div class="row mb-4 mr-4">
                        <a class="ml-auto btn btn-primary"
                           href="{{route('customs-code.create')}}">
                            Добавить
                        </a>
                    </div>
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
        } catch (e) {
            tHideSpinner();
        }
    }
</script>
