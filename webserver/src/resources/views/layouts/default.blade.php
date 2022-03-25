<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CertSign</title>
        <link rel="stylesheet" href={{ asset('css/bootstrap.min.css') }}>

    </head>
    <body>
        <x-header />

        @if ($errors->any())
            <div class="alert alert-dismissible alert-danger mx-5 mt-2">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="container-fluid">
            @yield('content')
        </div>
        
        <x-footer />
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    </body>
</html>
