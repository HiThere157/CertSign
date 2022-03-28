<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use App\Models\Certificate;
use App\Models\EncryptionKey;

class EncryptionKeyController extends Controller
{
    public function view($id)
    {
        $certificate = Certificate::find($id);

        if($certificate) {
            if(!Gate::allows('owns-cert', $certificate)) {
                return redirect()->route('certificates')->withErrors([
                    'error' => 'No Permission! Contact the owner of this certificate to get the private key.'
                ]);
            }
            
            return view('pages.encryptionkey', [
                'encryptionKey' => Crypt::decryptString($certificate->encryptionKey->key),
                'certificateId' => $certificate->id
            ]);
        }

        return redirect()->route('certificates');
    }
}
