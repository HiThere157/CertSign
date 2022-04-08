@extends('layouts.default')

@section('content')
    <div class="d-flex justify-content-between align-items-center mx-5">
        <h1 class="mt-3">Logs</h1>
    </div>

    <form action="{{ route('logs') }}">
        <div class="d-flex flex-wrap mb-3" style="column-gap: 1rem;">
            <div class="flex-grow-1">

                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="q" placeholder="Search Logs..." value="{{ $query }}">
                    <button id="typeBtn" class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Select Type</button>
                    <input type="text" name="type" hidden>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-item" style="cursor: pointer;">info</li>
                        <li class="dropdown-item" style="cursor: pointer;">warning</li>
                        <li class="dropdown-item" style="cursor: pointer;">error</li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="dropdown-item" style="cursor: pointer;">None Specified</li>
                    </ul>
                </div>

            </div>
            <div>
                <div class="d-flex">
                    <label for="search_start" class="col-form-label text-nowrap">Search Start:</label>
                    <input type="date" class="ms-2 form-control" id="search_start" name="start_time" />
                </div>
            </div>
            <div>
                <div class="d-flex">
                    <label for="search_end" class="col-form-label text-nowrap">Search End:</label>
                    <input type="date" class="ms-2 form-control" id="search_end" name="end_time" />
                </div>
            </div>
            <div>
                <button id="resetBtn" type="button" style="width: 10rem;" class="btn btn-secondary">Reset</button>
                <button type="submit" style="width: 10rem;" class="btn btn-primary">Search</button>
            </div>
        </div>
    </form>

    {{ $logs->links() }}
    <table class="table table-striped table-hover tablesorter">
        <thead>
            <tr>
                <th>Time</th>
                <th>Type</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td class="text-nowrap">{{ $log['time'] }}</td>

                    @if($log['type'] == 'INFO')
                        <td class="text-nowrap text-success">{{ $log['type'] }}</td>
                    @elseif($log['type'] == 'WARNING')
                        <td class="text-nowrap text-warning">{{ $log['type'] }}</td>
                    @elseif($log['type'] == 'ERROR')
                        <td class="text-nowrap text-danger">{{ $log['type'] }}</td>
                    @else
                        <td class="text-nowrap text-muted">{{ $log['type'] }}</td>
                    @endif

                    <td>{{ $log['description'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $logs->links() }}


    <script>
        function setDropdownValue(value) {
            if(value == ""){
                value = "None Specified";
            }

            $('#typeBtn').text(value);
            
            if(value == "None Specified"){
                value = "";
            }
            
            $('input[name="type"]').val(value);
        }

        $(document).ready(function() {
            //apply table sorter
            $(".table").tablesorter();

            //set previous dropdown value and start and end time
            setDropdownValue("{{ $type }}");
            $('#search_start').val("{{ $start_time }}");
            $('#search_end').val("{{ $end_time }}");

            //apply dropdown onclick
            $('.dropdown-item').click(function(){
                setDropdownValue($(this).text());
            });

            //reset button onclick
            $('#resetBtn').click(function(){
                $('input[name="q"]').val("");
                $('#search_start').val("");
                $('#search_end').val("");
                setDropdownValue("");
            });
        });
    </script>
@stop
