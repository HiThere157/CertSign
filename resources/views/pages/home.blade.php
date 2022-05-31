@extends('layouts.default')

@section('content')
    <div class="d-flex justify-content-between align-items-center mx-5">
        <h1 class="mt-3">Homepage</h1>
    </div>

    <div class="container-fluid d-flex flex-wrap">
        <div class="d-flex justify-content-center align-items-center flex-column rounded shadow m-3" style="width: 15%; min-width: 20rem; aspect-ratio: 1">
            <h1>
                {{ $expired_certificates }}
            </h1>
            <h5>
                Expired certificate{{ $expired_certificates == 1 ? '' : 's' }}
            </h5>
        </div>

        <div class="d-flex justify-content-center align-items-center flex-column rounded shadow m-3" style="width: 15%; min-width: 20rem; aspect-ratio: 1">
            <h1>
                {{ $all_certificates }}
            </h1>
            <h5>
                Active Certificate{{ $all_certificates == 1 ? '' : 's' }}
            </h5>
        </div>

        <div class="d-flex justify-content-center align-items-center flex-column rounded shadow m-3" style="width: 15%; min-width: 20rem; aspect-ratio: 1">
            <h1>
                {{ $all_users }}
            </h1>
            <h5>
                Registered User{{ $all_users == 1 ? '' : 's' }}
            </h5>
        </div>
    </div>
@stop
