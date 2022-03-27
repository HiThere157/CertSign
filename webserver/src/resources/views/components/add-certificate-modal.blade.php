<div class="modal fade" id="addCertificateModal" tabindex="-1" aria-labelledby="CertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="CertificateModalLabel">New Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="addCertificateForm" class="mx-5 px-5" action="certificates/add" method="post">
                    @csrf

                    <div class="d-flex flex-wrap mb-3" style="column-gap: 1rem;">
                        <div class="flex-grow-1">
                            <label for="name" class="col-form-label">Name:</label>
                            <input type="text" class="form-control" id="name" name="name" spellcheck="false" />
                        </div>
                        <div class="flex-grow-1">
                            <label for="created_by" class="col-form-label">Created by:</label>
                            @auth
                                <input type="text" class="form-control" id="created_by" value="{{ Auth::user()->username }}" disabled />
                            @endauth
                            @guest
                                <input type="text" class="form-control" id="created_by" value="" disabled />
                            @endguest
                        </div>
                    </div>

                    <div class="d-flex flex-wrap mb-3" style="column-gap: 1rem;">
                        <div class="flex-grow-1">
                            <label for="valid_from" class="col-form-label">Valid From:</label>
                            <input type="date" class="form-control" id="valid_from" disabled />
                        </div>
                        <div class="flex-grow-1">
                            <label for="valid_to" class="col-form-label">Valid To:</label>
                            <input type="date" class="form-control" id="valid_to" name="valid_to" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="issuer" class="col-form-label">Issuer:</label>
                        <div class="d-flex align-items-center" style="column-gap: 1rem;">
                            <select class="form-select" id="issuer" name="issuer">
                                <option value="">Select an issuer</option>
                                @foreach($root_certificates as $root_certificate)
                                    <option value="{{ $root_certificate->id }}">[{{ dechex($root_certificate->serial_number) }}] {{ $root_certificate->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-check">
                                <label class="form-check-label text-nowrap" for="self_signed"> Self Signed </label>
                                <input class="form-check-input" type="checkbox" id="self_signed" name="self_signed" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
                
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button form="addCertificateForm" type="submit" class="btn btn-primary">Generate</button>
            </div>
        </div>
    </div>
</div>