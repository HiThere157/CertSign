<div class="modal fade" id="changeOwnerModal" tabindex="-1" aria-labelledby="changeOwnerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeOwnerModalLabel">Change Owner</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="changeOwnerForm" class="mx-5 px-5" action="" method="post">
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
                </form>
            </div>
                
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button form="changeOwnerForm" type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>