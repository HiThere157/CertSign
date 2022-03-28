@extends('layouts.default')

@section('content')
    <div class="rounded shadow m-auto w-25 p-4 mt-3">
        <h1>Encryption Key (Id: {{ $certificateId }})</h1>

        <div class="input-group mb-3 mt-4">
            <input type="password" class="form-control" value="{{ $encryptionKey }}" readonly>
            <button id="toggleVisibilityBtn" class="btn btn-danger" type="button">Toggle Visibility</button>
        </div>

        <div class="d-flex">
            <a href="{{ route('certificates') }}" class="btn btn-secondary btn-block flex-grow-1 me-1">Back</a>
            <button id="copyToClipboardBtn" class="btn btn-primary btn-block flex-grow-1 ms-1">Copy to Clipboard</button>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#toggleVisibilityBtn').click(function() {
                var encryptionKey = $('#toggleVisibilityBtn').prev();
                if(encryptionKey.prop('type') === 'password') {
                    encryptionKey.attr('type', 'text');
                } else {
                    encryptionKey.attr('type', 'password');
                }
            });

            $('#copyToClipboardBtn').click(function() {
                navigator.clipboard.writeText($('#toggleVisibilityBtn').prev().val());

                this.innerText = 'Copied!';
                this.classList.add('btn-success');

                setTimeout(() => {
                    this.innerText = 'Copy to Clipboard';
                    this.classList.remove('btn-success');
                }, 5000);
            });
        });
    </script>
@stop
