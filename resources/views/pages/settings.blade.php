@extends('layouts.default')

@section('content')
    <div class="d-flex justify-content-between align-items-center mx-5">
        <h1 class="mt-3">Registered Users</h1>
    </div>
    <table class="table table-striped table-hover tablesorter">
        <thead>
            <tr>
                <th>Id</th>
                <th>Username</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Disabled At</th>
                <th class="sorter-false" style="width: 13rem;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr @class(['table-success' => Auth::user()->id == $user->id])>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>{{ $user->deleted_at }}</td>
                    <td class="text-end">
                        @if(Auth::user()->id != $user->id)
                            @if($user->deleted_at == null)
                                <a class="btn btn-secondary" href="{{ route('user.disable', $user->id) }}">Disable</a>

                                @if($user->is_admin == false)
                                    <a class="btn btn-danger" href="{{ route('user.promote', $user->id) }}">Make Admin</a>
                                @else
                                    <a class="btn btn-danger" href="{{ route('user.demote', $user->id) }}">Demote</a>
                                @endif
                                
                            @else
                                <a class="btn btn-primary" href="{{ route('user.enable', $user->id) }}">Enable</a>
                            @endif
                        @endif
                    </td>
                </tr>
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
