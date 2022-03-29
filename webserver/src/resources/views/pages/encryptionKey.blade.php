@extends('layouts.default')

@section('content')
    <div class="alert alert-warning w-50 mx-auto mt-4 d-flex align-items-center" role="alert">
        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img"><use xlink:href="#exclamation-triangle-fill"/></svg>
        Do not share your encryption key or the private key with anyone!
    </div>

    <div class="rounded shadow m-auto w-25 p-4 mt-3">
        <h1>Encryption Key (Id: {{ $certificateId }})</h1>

        <div class="input-group mb-3 mt-4">
            <input id="encryptionKey" type="password" class="form-control" value="{{ $encryptionKey }}" readonly>
            <button id="toggleVisibilityBtn" class="btn btn-danger" type="button">Toggle Visibility</button>
        </div>

        <div class="d-flex">
            <a href="{{ route('certificates') }}" class="btn btn-secondary btn-block flex-grow-1 me-1">Back</a>
            <button name="copyToClipboardBtn" data-copy-target="#encryptionKey" class="btn btn-primary btn-block flex-grow-1 ms-1">Copy to Clipboard</button>
        </div>
    </div>

    <div class="rounded shadow m-auto w-25 p-4 mt-3">
        <h1>Private Key (Id: {{ $certificateId }})</h1>

        <textarea id="privateKey" readonly hidden>{{ $privateKey }}</textarea>

        <div class="d-flex">
            <a href="{{ route('certificates') }}" class="btn btn-secondary btn-block flex-grow-1 me-1">Back</a>
            <button class="btn btn-primary btn-block flex-grow-1 ms-1 me-1" id="downloadPrivateKey">Download</button>
            <button name="copyToClipboardBtn" data-copy-target="#privateKey" class="btn btn-primary btn-block flex-grow-1 ms-1">Copy to Clipboard</button>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            //https://stackoverflow.com/a/64908345
            function download(content, filename){
                var a = document.createElement('a')
                var blob = new Blob([content], {'type': 'text/plain'})
                var url = URL.createObjectURL(blob)

                a.setAttribute('href', url)
                a.setAttribute('download', filename)
                a.click()
            }

            $('#toggleVisibilityBtn').click(function() {
                var visibilityReset;

                var encryptionKey = $(this).prev();
                if(encryptionKey.prop('type') === 'password') {
                    encryptionKey.attr('type', 'text');

                    clearTimeout(visibilityReset);
                    visibilityReset = setTimeout(function() {
                        encryptionKey.attr('type', 'password');
                    }, 10000);
                } else {
                    encryptionKey.attr('type', 'password');
                }
            });

            $('[name="copyToClipboardBtn"]').click(function() {
                var copyReset;

                navigator.clipboard.writeText($($(this).data('copy-target')).val());

                this.innerText = 'Copied!';
                this.classList.add('btn-success');

                clearTimeout(copyReset);
                copyReset = setTimeout(() => {
                    this.innerText = 'Copy to Clipboard';
                    this.classList.remove('btn-success');
                }, 5000);
            });

            $('#downloadPrivateKey').click(function() {
                download($('#privateKey').val(), 'private_key.key');
            });
        });
    </script>
@stop
