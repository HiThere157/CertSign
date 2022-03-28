@extends('layouts.default')

@section('content')
    <div class="alert alert-danger w-50 mx-auto mt-4 d-flex align-items-center" role="alert">
        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img"><use xlink:href="#exclamation-triangle-fill"/></svg>
        Please confirm your Password before you continue.
    </div>

    <form class="rounded shadow m-auto w-25 p-4 mt-3" style="min-width: 460px;" action="{{ route('password.confirm') }}" method="post">
        @csrf

        <div class="form-outline mb-4">
            <input type="password" id="reauthPassword" name="password" class="form-control" />
            <label class="form-label" for="reauthPassword">Confirm your Password</label>
        </div>

        <div class="d-flex">
            <a href="{{ route('certificates') }}" class="btn btn-secondary btn-block flex-grow-1 me-1">Back</a>
            <button type="submit" class="btn btn-primary btn-block flex-grow-1 ms-1">Continue</button>
        </div>
    </form>
@stop
