@extends('layouts.default')

@section('content')
    <div class="d-flex justify-content-between align-items-center mx-5">
        <h1 class="mt-3">Root Certificates</h1>
        <button type="button" name="addModalBtn" class="btn btn-primary" style="width: 15rem;" data-bs-toggle="modal" data-bs-target="#addCertificateModal" data-bs-selfSigned="true">Add Root Certificate</button>
    </div>
    <table class="table table-striped table-hover tablesorter">
        <caption>List of Root Certificates</caption>
        <thead class="border-0">
            <tr>
                <th style="width: 0;">Id</th>
                <th>Name</th>
                <th>Owner</th>
                <th>Valid From</th>
                <th>Valid To (Days remaining)</th>
                <th>Serial Number</th>
                <th class="sorter-false" style="width: 17rem;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @if(count($root_certificates) == 0)
                <tr>
                    <td colspan="7" class="text-center">No root certificates found. Mabe create a new one?</td>
                </tr>
            @endif

            @foreach($root_certificates as $root_certificate)
                <tr @class(['table-primary' => Gate::allows('has-permission', $root_certificate)])>
                    <td>{{ $root_certificate->id }}</td>
                    <td>
                        <span>{{ $root_certificate->name }}</span>
                        <span>
                            @if($root_certificate->daysValid() > 0)
                                <span class="badge bg-success">Valid</span>
                            @else
                                <span class="badge bg-danger">Expired</span>
                            @endif
                        </span>
                    </td>
                    <td>{{ $root_certificate->owner->username }}</td>
                    <td>{{ $root_certificate->valid_from }}</td>
                    <td>{{ $root_certificate->valid_to }} ({{ $root_certificate->daysValid() }} days)</td>
                    <td>0x{{ dechex($root_certificate->serial_number) }}</td>
                    <td class="text-end">
                        @can('owns-cert', $root_certificate)
                            <!-- only show transfer, if current user has permission -->
                            <a href="{{ route('permissions', $root_certificate->id) }}" class="btn btn-warning">Permissions</a>
                            <button name="deleteModalBtn" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCertificateModal" data-bs-deleteId="{{ $root_certificate->id }}">Delete</button>
                        @endcan
                        <button name="viewModalBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#viewCertificateModal" data-bs-viewId="{{ $root_certificate->id }}">View</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center mx-5">
        <h1 class="mt-3">Certificates</h1>
        <button type="button" name="addModalBtn" class="btn btn-primary" style="width: 15rem;" data-bs-toggle="modal" data-bs-target="#addCertificateModal" data-bs-selfSigned="false">Add Certificate</button>
    </div>
    <table class="table table-striped table-hover tablesorter">
        <caption>List of Certificates</caption>
        <thead class="border-0">
            <tr>
                <th style="width: 0;">Id</th>
                <th>Name</th>
                <th>Owner</th>
                <th>Issuer</th>
                <th>Valid From</th>
                <th>Valid To (Days remaining)</th>
                <th>Serial Number</th>
                <th class="sorter-false" style="width: 17rem;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @if(count($certificates) == 0)
                <tr>
                    <td colspan="8" class="text-center">No certificates found. Mabe create a new one?</td>
                </tr>
            @endif

            @foreach($certificates as $certificate)
                <tr>
                    <td>{{ $certificate->id }}</td>
                    <td>
                        <span>{{ $certificate->name }}</span>
                        <span>
                            @if($certificate->daysValid() > 0)
                                <span class="badge bg-success">Valid</span>
                            @else
                                <span class="badge bg-danger">Invalid</span>
                            @endif
                        </span>
                    </td>
                    <td>{{ $certificate->owner->username }}</td>
                    <td>[0x{{ dechex($certificate->issuer->serial_number) }}] {{ $certificate->issuer->name }}</td>
                    <td>{{ $certificate->valid_from }}</td>
                    <td>{{ $certificate->valid_to }} ({{ $certificate->daysValid() }} days)</td>
                    <td>0x{{ dechex($certificate->serial_number) }}</td>
                    <td class="text-end">
                        @can('owns-cert', $certificate)
                            <!-- only show transfer and delete, if current user has permission -->
                            <a href="{{ route('permissions', $certificate->id) }}" class="btn btn-warning">Permissions</a>
                            <button name="deleteModalBtn" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCertificateModal" data-bs-deleteId="{{ $certificate->id }}">Delete</button>
                        @endcan
                        <button name="viewModalBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#viewCertificateModal" data-bs-viewId="{{ $certificate->id }}">View</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

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
                                        <option value="{{ $root_certificate->id }}">[0x{{ dechex($root_certificate->serial_number) }}] {{ $root_certificate->name }}</option>
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
    
    <x-view-certificate-modal />
    <x-delete-certificate-modal />

    <script>
        //https://stackoverflow.com/a/64908345
        function download(content, filename){
            var a = document.createElement('a')
            var blob = new Blob([content], {'type': 'text/plain'})
            var url = URL.createObjectURL(blob)

            a.setAttribute('href', url)
            a.setAttribute('download', filename)
            a.click()
        }

        function addRemoveSanBtn (element) {
            $(element).parent().remove();
        }

        function updateAddModal (modalOpening){
            //if modal is opened, override current checkbox attribute
            var selfSigned = modalOpening ? $(this).attr('data-bs-selfSigned') === 'true' : $(this).prop('checked');

            var issuerInput = $('#addIssuer');
            var sanInput = $('#addSanInput');
            issuerInput.prop('disabled', selfSigned);
            sanInput.prop('disabled', selfSigned);

            if(selfSigned) { 
                issuerInput.val(''); 
                sanInput.val(''); 

                $('#addSanList').find('div').remove();
            }

            $('#addSelf_signed').prop('checked', selfSigned);
            $('#addCertificateModalLabel').text(selfSigned ? 'Add Root Certificate' : 'Add Certificate');

            //set default values of valid_from and valid_to
            $('#addValid_from').val(new Date().toISOString().substring(0, 10));
            $('#addValid_to').val(new Date(new Date().getTime() + 365 * 24 * 60 * 60 * 1000).toISOString().substring(0, 10));
        }

        async function updateViewModal (id){
            $('#viewName').val('');
            $('#viewOwner').val('');
            $('#viewValid_from').val('');
            $('#viewValid_to').val('');

            $('#viewSN').val('');
            $('#viewHash').val('');
            $('#viewSigType').val('');

            $('#viewSanList').find('input').remove();
            $('#viewSubjectList').find('input').remove();

            $('#viewIssuer').val('');
            $('#viewIssuerSN').val('');

            $('#viewIssuerCertificate').unbind();

            $('#viewFileCertificate').find('textarea').remove();
            $('#viewFileKey').find('textarea').remove();
            $('#viewFileCsr').find('textarea').remove();
            $('#viewFileConfig').find('textarea').remove();

            $('[name="viewSecrets"]').prop('href', '');

            // ^reset all values
            var response = await fetch("{{ route('certificate.view', ':id')}}".replace(':id', id));
            var certificateInfo = await response.json();

            $('#viewCertificateModalLabel').text((certificateInfo.certificate.self_signed ? 'View Root Certificate' : 'View Certificate') + " (Id: " + certificateInfo.certificate.id + ")");
            $('#viewName').val(certificateInfo.certificate.name);
            $('#viewOwner').val(certificateInfo.owner);
            $('#viewValid_from').val(certificateInfo.certificate.valid_from);
            $('#viewValid_to').val(certificateInfo.certificate.valid_to);

            $('#viewSN').val('0' + Number(certificateInfo.certificate.serial_number).toString(16));
            $('#viewHash').val(certificateInfo.decoded.hash);
            $('#viewSigType').val(certificateInfo.decoded.signatureTypeSN);

            certificateInfo.decoded.extensions.subjectAltName?.split(', ').forEach(san => {
                $('#viewSanList').append('<input type="text" class="form-control mb-2" value="' + san.replace(':', ': ') + '" readonly >');
            });

            Object.entries(certificateInfo.decoded.subject).forEach(subject => {
                $('#viewSubjectList').append('<input type="text" class="form-control mb-2" value="' + subject.join(': ') + '" readonly >');
            });

            $('#viewIssuer').val(certificateInfo.issuer.name);
            $('#viewIssuerSN').val('0' + Number(certificateInfo.issuer.serial_number).toString(16));

            $('#viewIssuerCertificate').click(function() {
                updateViewModal(certificateInfo.issuer.id);
            });

            var textareaHTML = '<textarea class="form-control w-75 mx-auto mt-2" onclick="this.focus();this.select()" style="height: 20rem" readonly></textarea>';
            $(textareaHTML).text(certificateInfo.files.certificate).appendTo('#viewFileCertificate');
            $(textareaHTML).text(certificateInfo.files.private_key).appendTo('#viewFileKey');
            $(textareaHTML).text(certificateInfo.files.csr).appendTo('#viewFileCsr');
            $(textareaHTML).text(certificateInfo.files.cnf).appendTo('#viewFileConfig');

            $('[name="viewSecrets"]').prop('href', "{{ route('secrets', ':id')}}".replace(':id', id));
        }

        $(document).ready(function() {
            //apply table sorter
            $(".table").tablesorter();

            //set default values of valid_from and valid_to, and disable issuer input if self-signed
            $('[name="addModalBtn"]').click(function() {
                updateAddModal.call(this, true);
            });

            //add Certificate self signed checkbox listener
            $('#addCertificateModal').find('[name="self_signed"]').change(function() {
                updateAddModal.call(this, false);
            });

            //add new SAN input listener
            $('#addSanBtn').click(function() {
                var sanInput = $('#addSanInput');
                var sanInputValue = sanInput.val();

                if(sanInputValue != '') {
                    $('#addSanList').append(
                        '<div class="input-group mb-2">' + 
                            '<input type="text" class="form-control" name="san[]" value="' + sanInputValue + '" readonly >' +
                            '<button class="btn btn-outline-danger" type="button" onclick="addRemoveSanBtn(this)">Remove</button>' +
                        '</div>'
                    );
                    sanInput.val('');
                }
            });

            //preventDefault on addSanInput
            $('#addSanInput').keypress(function(e) {
                if(e.keyCode  == 13) {
                    e.preventDefault();
                    $('#addSanBtn').click();
                    return false;
                }
            });

            // add valid_to increment buttons listener
            function getCurrentValidTo() {
                return new Date($('#addValid_to').val());
            }
            // 1 day
            $('#addIncrement0').click(function() {
                var validToDate = getCurrentValidTo();
                validToDate.setDate(validToDate.getDate() + 1);
                $('#addValid_to').val(validToDate.toISOString().substring(0, 10));
            });
            // 1 month
            $('#addIncrement1').click(function() {
                var validToDate = getCurrentValidTo();
                validToDate.setMonth(validToDate.getMonth() + 1);
                $('#addValid_to').val(validToDate.toISOString().substring(0, 10));
            });
            // 1 year
            $('#addIncrement2').click(function() {
                var validToDate = getCurrentValidTo();
                validToDate.setFullYear(validToDate.getFullYear() + 1);
                $('#addValid_to').val(validToDate.toISOString().substring(0, 10));
            });
            
            //clear all input fields before opening, and set all values
            $('[name="viewModalBtn"]').click(function() {
                updateViewModal($(this).attr('data-bs-viewId'));
            });

            //listener for download button
            $('#viewDownloadFiles').click(function() {
                download($('#viewFileCertificate').find('textarea').val(), 'certificate.cer');
            });

            //set confirm button to correct route
            $('[name="deleteModalBtn"]').click(function() {
                $('#deleteCertificateBtn').prop('href', "{{ route('certificate.delete', ':id')}}".replace(':id', $(this).attr('data-bs-deleteId')));
            });
        });
    </script>
@stop
