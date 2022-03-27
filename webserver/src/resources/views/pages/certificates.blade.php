@extends('layouts.default')

@section('content')
    <div class="d-flex justify-content-between align-items-center mx-5">
        <h1>Root Certificates</h1>
        <button type="button" class="btn btn-primary" style="width: 15rem;" data-bs-toggle="modal" data-bs-target="#addCertificateModal" data-bs-selfSigned="true">Add Root Certificate</button>
    </div>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Created by</th>
                <th>Valid From</th>
                <th>Valid To (Days remaining)</th>
                <th>Serial Number</th>
                <th style="width: 10rem;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($root_certificates as $root_certificate)
                <tr>
                    <td>{{ $root_certificate->id }}</td>
                    <td>
                        <span>{{ $root_certificate->name }}</span>
                        <span>
                            @if($root_certificate->daysValid() > 0)
                                <span class="badge bg-success">Valid</span>
                            @else
                                <span class="badge bg-danger">Invalid</span>
                            @endif
                        </span>
                    </td>
                    <td>{{ $root_certificate->user->username }}</td>
                    <td>{{ $root_certificate->valid_from }}</td>
                    <td>{{ $root_certificate->valid_to }} ({{ $root_certificate->daysValid() }} days)</td>
                    <td>{{ dechex($root_certificate->serial_number) }}</td>
                    <td>
                        <a href="#" class="btn btn-primary">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr class="my-3">

    <div class="d-flex justify-content-between align-items-center mx-5">
        <h1>Certificates</h1>
        <button type="button" class="btn btn-primary" style="width: 15rem;" data-bs-toggle="modal" data-bs-target="#addCertificateModal" data-bs-selfSigned="false">Add Certificate</button>
    </div>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Created by</th>
                <th>Issuer</th>
                <th>Valid From</th>
                <th>Valid To (Days remaining)</th>
                <th>Serial Number</th>
                <th style="width: 10rem;">Actions</th>
            </tr>
        </thead>
        <tbody>
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
                    <td>{{ $certificate->user->username }}</td>
                    <td>[{{ dechex($certificate->issuer->serial_number) }}] {{ $certificate->issuer->name }}</td>
                    <td>{{ $certificate->valid_from }}</td>
                    <td>{{ $certificate->valid_to }} ({{ $certificate->daysValid() }} days)</td>
                    <td>{{ dechex($certificate->serial_number) }}</td>
                    <td>
                        <a href="#" class="btn btn-primary">View</a>
                        <a href="certificates/delete/{{ $certificate->id }}" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <x-add-certificate-modal />

    <script>
        $(document).ready(function() {
            function updateModal (modalOpening){
                //if modal is opened, override current checkbox attribute
                var selfSigned = modalOpening ? $(this).attr('data-bs-selfSigned') == "true" : $(this).prop('checked');

                var issuerInput = $('#addCertificateModal').find('#issuer');
                issuerInput.prop('disabled', selfSigned);
                $('#addCertificateModal').find('[name="self_signed"]').prop('checked', selfSigned);
                $('#CertificateModalLabel').text(selfSigned ? "Add Root Certificate" : "Add Certificate");

                if(selfSigned) { issuerInput.val(""); }

                $('#addCertificateModal').find('[id="valid_from"]').val(new Date().toISOString().substring(0, 10));
                $('#addCertificateModal').find('[name="valid_to"]').val(new Date(new Date().getTime() + 365 * 24 * 60 * 60 * 1000).toISOString().substring(0, 10));
            }

            $('[data-bs-toggle="modal"]').click(function() {
                updateModal.call(this, true);
            });

            $('#addCertificateModal').find('[name="self_signed"]').change(function() {
                updateModal.call(this, false);
            });
        });

    </script>

@stop
