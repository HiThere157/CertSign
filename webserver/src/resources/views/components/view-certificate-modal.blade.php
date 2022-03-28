<div class="modal fade" id="viewCertificateModal" tabindex="-1" aria-labelledby="viewCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCertificateModalLabel">View Certificate</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="mx-5 px-5">

                    <div class="d-flex flex-wrap mb-3" style="column-gap: 1rem;">
                        <div class="flex-grow-1">
                            <label for="viewName" class="col-form-label">Name:</label>
                            <input type="text" class="form-control" id="viewName" spellcheck="false" readonly />
                        </div>
                        <div class="flex-grow-1">
                            <label for="viewCreated_by" class="col-form-label">Created by:</label>
                            <input type="text" class="form-control" id="viewCreated_by" readonly />
                        </div>
                        <div class="flex-grow-1">
                            <label for="viewValid_from" class="col-form-label">Valid From:</label>
                            <input type="date" class="form-control" id="viewValid_from" readonly />
                        </div>
                        <div class="flex-grow-1">
                            <label for="viewValid_to" class="col-form-label">Valid To:</label>
                            <input type="date" class="form-control" id="viewValid_to" name="valid_to" readonly />
                        </div>
                    </div>
                    <div class="d-flex flex-wrap mb-3" style="column-gap: 1rem;">
                        <div class="flex-grow-1">
                            <label for="viewSN" class="col-form-label">Serial Number:</label>
                            <input type="text" class="form-control" id="viewSN" spellcheck="false" readonly />
                        </div>
                        <div class="flex-grow-1">
                            <label for="viewHash" class="col-form-label">Hash</label>
                            <input type="text" class="form-control" id="viewHash" readonly />
                        </div>
                        <div class="flex-grow-1">
                            <label for="viewSigType" class="col-form-label">Signature Type</label>
                            <input type="text" class="form-control" id="viewSigType" readonly />
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex flex-wrap mb-3" style="column-gap: 1rem;">
                        <div class="flex-grow-1">
                            <div class="mb-3">
                                <label for="viewSanList" class="col-form-label">Subject Alternative Names:</label>
                                <div id="viewSanList"></div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="mb-3">
                                <label for="viewSubjectList" class="col-form-label">Subjects:</label>
                                <div id="viewSubjectList"></div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex flex-wrap mb-3" style="column-gap: 1rem;">
                        <div class="flex-grow-1">
                            <label for="viewIssuer" class="col-form-label">Issuer:</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="viewIssuer" spellcheck="false" readonly />
                                <button class="btn btn-primary" id="viewIssuerCertificate">View</button>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <label for="viewIssuerSN" class="col-form-label">Issuer Serial Number:</label>
                            <input type="text" class="form-control" id="viewIssuerSN" readonly />
                        </div>
                    </div>
                    <hr>
                    <nav>
                        <div class="nav nav-tabs justify-content-center" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="viewCertificateTab" data-bs-toggle="tab" data-bs-target="#viewFileCertificate" role="tab" aria-selected="true">Certificate</button>
                            <button class="nav-link" id="viewKeyTab" data-bs-toggle="tab" data-bs-target="#viewFileKey" role="tab" aria-selected="false">Private Key</button>
                            <button class="nav-link" id="viewCsrTab" data-bs-toggle="tab" data-bs-target="#viewFileCsr" role="tab" aria-selected="false">CSR</button>
                            <button class="nav-link" id="viewConfigTab" data-bs-toggle="tab" data-bs-target="#viewFileConfig" role="tab" aria-selected="false">Config</button>
                            <button class="nav-link" id="viewDownloadTab" data-bs-toggle="tab" data-bs-target="#viewFileDownload" role="tab" aria-selected="false">Download</button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="viewFileCertificate" role="tabpanel"></div>
                        <div class="tab-pane fade" id="viewFileKey" role="tabpanel"></div>
                        <div class="tab-pane fade" id="viewFileCsr" role="tabpanel"></div>
                        <div class="tab-pane fade" id="viewFileConfig" role="tabpanel"></div>
                        <div class="tab-pane fade d-flex flex-column align-items-center" id="viewFileDownload" role="tabpanel">
                            <span class="d-block mt-2">Download Certificate and Encrypted Private Key:</span>
                            <div class="d-flex">
                                <button class="btn btn-primary me-1" id="viewDownloadFiles">Download</button>
                                <a href="" id="viewEncryptionKey" class="btn btn-warning ms-1">Show Encryption Password</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
                
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>