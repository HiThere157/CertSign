@extends('layouts.default')

@section('content')
    @if($self_signed)
        <div class="alert alert-warning w-75 mx-auto mt-4 d-flex align-items-center" role="alert" style="min-width: 30rem;">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img"><use xlink:href="#exclamation-triangle-fill"/></svg>
            User with Permission are able to use this Certificate as an issuer for other certificates.
        </div>

        <div class="d-flex justify-content-between align-items-center mx-5 mt-3">
            <h1>Manage Additional Permissions <span class="text-nowrap">(Id: {{ $id }})</span></h1>
            <div>
                <button id="addPermissionBtn" type="button" class="btn btn-primary ms-1" style="width: 15rem;" data-bs-toggle="modal" data-bs-target="#addPermissionModal">Add User Permission</button>
            </div>
        </div>
        <table class="table table-striped table-hover tablesorter">
            <caption>List of Permissions</caption>
            <thead class="border-0">
                <tr>
                    <th>Name</th>
                    <th>Added by</th>
                    <th>Added</th>
                    <th class="sorter-false" style="width: 0;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(count($user_permissions) == 0)
                    <tr>
                        <td colspan="8" class="text-center">No permissions found.</td>
                    </tr>
                @endif

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
    @endif

    <div class="alert alert-danger w-75 mx-auto mt-5 d-flex align-items-center" role="alert" style="min-width: 30rem;">
        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img"><use xlink:href="#exclamation-triangle-fill"/></svg>
        By changing the owner of this certificate, you will lose access to the encryption key and the private key. This action cannot be undone!
    </div>

    <div class="rounded shadow m-auto w-50 p-4 mt-3" style="min-width: 30rem;">
        <h1>Change Certificate Owner <span class="text-nowrap">(Id: {{ $id }})</span></h1>

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

    <div class="modal fade" id="addPermissionModal" tabindex="-1" aria-labelledby="addPermissionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPermissionModalLabel">New Permission</h5>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="addPermissionForm" class="mx-5 px-5" action="{{ route('permissions.add', $id) }}" method="post">
                        @csrf

                        <div class="mb-3">
                            <label for="newPermission" class="col-form-label">User to add Permission for:</label>
                            <select class="form-select" id="newPermission" name="addUser">
                                <option value="">Select a new user</option>
                                @foreach($all_users as $user)
                                    @if($user->id != Auth::user()->id)
                                        <option value="{{ $user->id }}">{{ $user->username }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                    </form>
                </div>
                    
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button form="addPermissionForm" type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <x-delete-permission-modal />

    <script>
        $(document).ready(function() {
            //apply table sorter
            $(".table").tablesorter();

            $('[name="deleteModalBtn"]').click(function() {
                $('#deletePermissionBtn').prop('href', "{{ route('permissions.delete', ':id')}}".replace(':id', $(this).attr('data-bs-deleteId')));
            });
        });
    </script>
@stop
