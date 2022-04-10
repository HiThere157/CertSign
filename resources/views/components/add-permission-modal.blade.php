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