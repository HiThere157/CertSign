@extends('layouts.default')

@section('content')
    <div class="d-flex justify-content-between align-items-center mx-5">
        <h1 class="mt-3">Deleted Certificates</h1>
    </div>
    <table class="table table-striped table-hover tablesorter">
        <caption>List of Deleted Certificates</caption>
        <thead class="border-0">
            <tr>
                <th style="width: 0;">Id</th>
                <th>Name</th>
                <th>Owner</th>
                <th>Issuer</th>
                <th>Valid From</th>
                <th>Valid To (Days remaining)</th>
                <th>Serial Number</th>
                <th class="sorter-false" style="width: 0;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @if(count($certificates) == 0)
                <tr>
                    <td colspan="8" class="text-center">No deleted Certificates found.</td>
                </tr>
            @endif

            @foreach($certificates as $certificate)
                @can('owns-cert', $certificate)
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
                            <a class="btn btn-primary" href="{{ route('certificate.restore', $certificate->id) }}">Restore</a>
                        </td>
                    </tr>
                @endcan
            @endforeach
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            //apply table sorter
            $(".table").tablesorter();
        });
    </script>
@stop
