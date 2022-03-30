@extends('layouts.default')

@section('content')
    @if($self_signed)
        <div class="alert alert-warning w-75 mx-auto mt-4 d-flex align-items-center" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img"><use xlink:href="#exclamation-triangle-fill"/></svg>
            User with Permission are able to use this Certificate as an issuer for other certificates.
        </div>

        <div class="d-flex justify-content-between align-items-center mx-5 mt-3">
            <h1>Manage Additional Permissions (Id: {{ $id }})</h1>
            <div>
                <a href="{{ route('certificates') }}" class="btn btn-secondary me-1" style="width: 15rem;">Back</a>
                <button id="addPermissionBtn" type="button" class="btn btn-primary ms-1" style="width: 15rem;" data-bs-toggle="modal" data-bs-target="#addPermissionModal">Add User Permission</button>
            </div>
        </div>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Added by</th>
                    <th>Added</th>
                    <th style="width: 17rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($user_permissions as $permission)
                    <tr>
                        <td>{{ $permission->user->username }}</td>
                        <td>{{ $permission->addedBy->username }}</td>
                        <td>{{ $permission->created_at }}</td>
                        <td>
                            <button name="deleteModalBtn" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deletePermissionModal" data-bs-deleteId="{{ $permission->id }}">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <hr class="mt-5 mb-5">
    @endif

    <div class="alert alert-danger w-75 mx-auto mt-3 d-flex align-items-center" role="alert">
        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img"><use xlink:href="#exclamation-triangle-fill"/></svg>
        By changing the owner of this certificate, you will lose access to the encryption key and the private key. This action cannot be undone!
    </div>

    <div class="rounded shadow m-auto w-50 p-4 mt-3">
        <h1>Change Certificate Owner (Id: {{ $id }})</h1>

        <form id="changeOwnerForm" class="mb-3 mt-3" action="{{ route('permissions.changeOwner', $id) }}" method="post">
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

    <x-add-permission-modal :allUsers="$all_users" :id="$id" />
    <x-delete-permission-modal />

    <script>
        $(document).ready(function() {
            $('[name="deleteModalBtn"]').click(function() {
                $('#deletePermissionBtn').prop('href', "{{ route('permissions.delete', ':id')}}".replace(':id', $(this).attr('data-bs-deleteId')));
            });
        });
    </script>

@stop
