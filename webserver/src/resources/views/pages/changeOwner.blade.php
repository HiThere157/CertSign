@extends('layouts.default')

@section('content')
    <div class="rounded shadow m-auto w-50 p-4 mt-3">
        <h1>Change Certificate Owner (Id: {{ $certificateId }})</h1>

        <form id="changeOwnerForm" class="mb-3 mt-4" action="{{ route('certificate.changeOwner', $certificateId) }}" method="post">
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
