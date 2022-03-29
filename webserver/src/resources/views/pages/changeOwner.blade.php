@extends('layouts.default')

@section('content')
    <div class="alert alert-warning w-75 mx-auto mt-4 d-flex align-items-center" role="alert">
        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img"><use xlink:href="#exclamation-triangle-fill"/></svg>
        By changing the owner of this certificate, you will lose access to the encryption key and the private key. This action cannot be undone!
    </div>

    <div class="rounded shadow m-auto w-50 p-4 mt-3">
        <h1>Change Certificate Owner (Id: {{ $certificateId }})</h1>

        <form id="changeOwnerForm" class="mb-3 mt-3" action="{{ route('certificate.changeOwner', $certificateId) }}" method="post">
            @csrf

            <div class="mb-3">
                <label for="newOwner" class="col-form-label">New Owner:</label>
                <select class="form-select" id="newOwner" name="newOwner">
                    <option value="">Select a new user</option>
                    @foreach($all_users as $user)
                        <option value="{{ $user->id }}">{{ $user->username }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex">
                <a href="{{ route('certificates') }}" class="btn btn-secondary btn-block flex-grow-1 me-1">Back</a>
                <button type="submit" class="btn btn-primary btn-block flex-grow-1 ms-1">Save</button>
            </div>
        </form>
    </div>
@stop
