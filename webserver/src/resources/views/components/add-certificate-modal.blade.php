<div class="modal fade" id="addCertificateModal" tabindex="-1" aria-labelledby="addCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCertificateModalLabel">New Certificate</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="addCertificateForm" class="mx-5 px-5" action="{{ route('certificate.add') }}" method="post">
                    @csrf

                    <div class="d-flex flex-wrap mb-3" style="column-gap: 1rem;">
                        <div class="flex-grow-1">
                            <label for="addName" class="col-form-label">Name:</label>
                            <input type="text" class="form-control" id="addName" name="name" spellcheck="false" />
                        </div>
                        <div class="flex-grow-1">
                            <label for="addCreated_by" class="col-form-label">Created by:</label>
                            @auth
                                <input type="text" class="form-control" id="addCreated_by" value="{{ Auth::user()->username }}" disabled />
                            @endauth
                            @guest
                                <input type="text" class="form-control" id="addCreated_by" value="" disabled />
                            @endguest
                        </div>
                    </div>

                    <div class="d-flex flex-wrap mb-3" style="column-gap: 1rem;">
                        <div class="flex-grow-1">
                            <label for="addValid_from" class="col-form-label">Valid From:</label>
                            <input type="date" class="form-control" id="addValid_from" disabled />
                        </div>
                        <div class="flex-grow-1">
                            <label for="addValid_to" class="col-form-label">Valid To:</label>
                            <div class="input-group mb-3">
                                <input type="date" class="form-control" id="addValid_to" name="valid_to" />
                                <button class="btn btn-outline-secondary" type="button" id="addIncrement0">+1 Day</button>
                                <button class="btn btn-outline-secondary" type="button" id="addIncrement1">+1 Month</button>
                                <button class="btn btn-outline-secondary" type="button" id="addIncrement2">+1 Year</button>
                            </div>

                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="addIssuer" class="col-form-label">Issuer:</label>
                        <div class="d-flex align-items-center" style="column-gap: 1rem;">
                            <select class="form-select" id="addIssuer" name="issuer">
                                <option value="">Select an issuer</option>
                                @foreach($root_certificates as $root_certificate)
                                    <option value="{{ $root_certificate->id }}">[0{{ dechex($root_certificate->serial_number) }}] {{ $root_certificate->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-check">
                                <label class="form-check-label text-nowrap" for="addSelf_signed"> Self Signed </label>
                                <input class="form-check-input" type="checkbox" id="addSelf_signed" name="self_signed" />
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label for="addSanInput" class="col-form-label">Subject Alt Names:</label>
                        <div class="input-group mb-3">
                            <input type="text" id="addSanInput" class="form-control" placeholder="*.example.com" aria-label="Add new SAN">
                            <button class="btn btn-primary" id="addSanBtn" type="button">Add</button>
                        </div>

                        <div id="addSanList"></div>
                    </div>
                </form>
            </div>
                
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button form="addCertificateForm" type="submit" class="btn btn-primary">Generate</button>
            </div>
        </div>
    </div>
</div>