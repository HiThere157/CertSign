<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\Models\Certificate;
use App\Models\EncryptionKey;
use App\Models\User;

class CertificateController extends Controller
{
    //GET: index page for certificate management
    public function certificates_index()
    {
        Log::info('[CertificateController@certificates_index] User ' . auth()->user()->username . ' accessed the certificates page.');
        return view('pages.certificates', [
            'root_certificates' => Certificate::all()->where('self_signed', true),
            'certificates' => Certificate::all()->where('self_signed', false)
        ]);
    }

    //GET: index page for viewing a encryption key or private key
    public function encryptionKey_index($id)
    {
        $certificate = Certificate::find($id);
        
        if($certificate) {
            if(!Gate::allows('owns-cert', $certificate)) {
                return redirect()->route('certificates')->withErrors([
                    'error' => 'No Permission! Contact the owner of this certificate to get the private key.'
                ]);
            }
            
            $storagePath = 'certificates/' . dechex($certificate->serial_number);
            
            $encryptionKey = Crypt::decryptString($certificate->encryptionKey->key);
            $privateKey = openssl_pkey_get_private(Storage::disk('local')->get($storagePath . '/cert.key'), $encryptionKey);
            openssl_pkey_export($privateKey, $privateKeyOut, null, ['config' => storage_path('app/' . $storagePath . '/openssl.cnf')]);
            
            Log::info('[CertificateController@encryptionKey_index] User ' . auth()->user()->username . ' accessed the encryption key page for certificate ' . $id . '.');
            return view('pages.encryptionkey', [
                'encryptionKey' => $encryptionKey,
                'privateKey' => $privateKeyOut,
                'certificateId' => $certificate->id
            ]);
        }

        return redirect()->route('certificates');
    }

    //GET: index page for deleted certificates
    public function deleted_index()
    {
        Log::info('[CertificateController@deleted_index] User ' . auth()->user()->username . ' accessed the deleted certificates page.');
        return view('pages.restore', [
            'certificates' => Certificate::onlyTrashed()->get()
        ]);
    }

    //GET: get all information about a certificate
    public function get_information($id)
    {
        $db_certificate = Certificate::find($id);

        if(!$db_certificate) {
            return ['error' => 'Certificate not found'];
        }

        $storagePath = 'certificates/' . dechex($db_certificate->serial_number);
        $certificate = Storage::disk('local')->get($storagePath . '/cert.cer');
        $privateKey = Storage::disk('local')->get($storagePath . '/cert.key');
        $csr = Storage::disk('local')->get($storagePath . '/cert.csr');
        $cnf = Storage::disk('local')->get($storagePath . '/openssl.cnf');
        
        $certificate_decoded = openssl_x509_parse($certificate);

        Log::info('[CertificateController@get_information] User ' . auth()->user()->username . ' accessed the certificate information page for certificate ' . $id . '.');
        return [
            'certificate' => $db_certificate,
            'decoded' => $certificate_decoded,
            'issuer' => Certificate::find($db_certificate->issuer_id ?? $db_certificate->id),
            'files' => [
                'certificate' => $certificate,
                'private_key' => Gate::allows('owns-cert', $db_certificate) ? $privateKey : "No Permission!\n\nContact the owner of this certificate to get the private key.",
                'csr' => $csr,
                'cnf' => $cnf
            ]
        ];
    }

    //GET: soft delete a certificate
    public function delete($id)
    {
        $certificate = Certificate::find($id);
        if ($certificate) {
            if(!Gate::allows('owns-cert', $certificate)) {
                return redirect()->route('certificates')->withErrors([
                    'error' => 'No Permission! Contact the owner of this certificate to delete it.'
                ]);
            }

            Log::info('[CertificateController@delete] User ' . auth()->user()->username . ' deleted certificate ' . $id . '.');
            $certificate->encryptionKey->delete();
            $certificate->delete();
        }

        return redirect()->route('certificates');
    }

    //GET: restore a soft deleted certificate
    public function restore($id)
    {
        $certificate = Certificate::withTrashed()->find($id);
        if ($certificate) {
            if(!Gate::allows('owns-cert', $certificate)) {
                return redirect()->route('certificates')->withErrors([
                    'error' => 'No Permission! Contact the owner of this certificate to restore it.'
                ]);
            }

            Log::info('[CertificateController@restore] User ' . auth()->user()->username . ' restored certificate ' . $id . '.');
            $certificate->restore();
            $certificate->encryptionKey()->restore();
        }

        return redirect()->route('certificates');
    }

    //POST: create a new certificate
    public function add(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'valid_to' => 'required'
        ]);

        if($request->input('issuer') == '' && $request->input('self_signed') != 'on') {
            return redirect()->route('certificates')->withErrors([
                'issuer' => 'Issuer is required for non self-signed certificates'
            ]);
        }

        $issuer = Certificate::find($request->input('issuer'));
        if($issuer) {
            if(!Gate::allows('has-permission', $issuer)) {
                return redirect()->route('certificates')->withErrors([
                    'issuer' => 'No Permission! Contact the owner of this certificate to get permission to sign with this Certificate.'
                ]);
            }

            if(!$issuer->self_signed) {
                return redirect()->route('certificates')->withErrors([
                    'issuer' => 'Cannot sign with a non self-signed certificate'
                ]);
            }
        }

        $certificate = new Certificate();
        $certificate->name = $request->input('name');
        $certificate->created_by_id = auth()->user()->id;
        $certificate->owner_id = auth()->user()->id;
        $certificate->valid_from = date("Y-m-d");
        $certificate->valid_to = $request->input('valid_to');
        $certificate->serial_number = $this->generateNewSerial();
        $certificate->self_signed = $request->input('self_signed') == 'on';
        $certificate->save();

        $certificate->issuer_id = $issuer->id ?? $certificate->id;
        $certificate->save();

        Log::info('[CertificateController@add] User ' . auth()->user()->username . ' created certificate ' . $certificate->id . '.');
        $this->generateNewCertificate($certificate, $issuer, $request->input('san'));

        return redirect()->route('certificates');
    }

    private function generateNewSerial()
    {
        $usedSerials = Storage::disk('local')->get('certificates/serials.srl');
        $usedSerials = explode(PHP_EOL, $usedSerials);
        
        $newSerial = rand(100000, 999999);
        while(in_array($newSerial, $usedSerials)) {
            $newSerial = rand(100000, 999999);
        }

        Storage::disk('local')->append('certificates/serials.srl', $newSerial);
        return $newSerial;
    }
    private function generateNewEncryptionKey()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 30; $i++) {
            $randstring .= $characters[rand(0, strlen($characters)-1)];
        }
        return $randstring;
    }
    private function generateNewCertificate(Certificate $certificate, $issuer, $subjectAltNames)
    {
        $storagePath = 'certificates/' . dechex($certificate->serial_number);
        $confPath = storage_path('app/' . $storagePath . '/openssl.cnf');

        $configContent = Blade::render(Storage::disk('local')->get('certificates\openssl.blade.cnf'), [
            'commonName' => $certificate->name,
            'created_by' => $certificate->creator->username,
            'subjects' => $subjectAltNames ?? [],
            'ca' => $certificate->self_signed ? 'TRUE' : 'FALSE',
        ]);
        Storage::disk('local')->put($storagePath . '/openssl.cnf', $configContent);
                
        $privateKey = openssl_pkey_new([
            'config' => $confPath,
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        
        $csr = openssl_csr_new(['commonName' => $certificate->name, 'organizationalUnitName' => $certificate->creator->username], $privateKey, [
            'config' => $confPath,
            'digest_alg' => 'sha256'
        ]);
        
        if($issuer) {
            $issuerStoragePath = 'certificates/' . dechex($issuer->serial_number);
            $issuerCertificate = Storage::disk('local')->get($issuerStoragePath . '/cert.cer');
            $issuerPrivateKey = openssl_pkey_get_private(Storage::disk('local')->get($issuerStoragePath . '/cert.key'), Crypt::decryptString(Certificate::find($certificate->issuer_id)->encryptionKey->key));
        }

        $x509 = openssl_csr_sign(
            $csr, 
            $certificate->self_signed ? null : $issuerCertificate, 
            $certificate->self_signed ? $privateKey : $issuerPrivateKey,
            $certificate->daysValid(), 
            [
                'config' => $confPath,
                'x509_extensions' => 'v3_req',
                'digest_alg' => 'sha256'
            ], 
            $certificate->serial_number
        );

        $encryptionKey = $this->generateNewEncryptionKey();

        openssl_pkey_export($privateKey, $privateKeyOut, $encryptionKey, ['config' => $confPath]);
        openssl_csr_export($csr, $csrOut);
        openssl_x509_export($x509, $certOut);

        Storage::disk('local')->put($storagePath . '/cert.key', $privateKeyOut);
        Storage::disk('local')->put($storagePath . '/cert.csr', $csrOut);
        Storage::disk('local')->put($storagePath . '/cert.cer', $certOut);

        $key = new EncryptionKey();
        $key->certificate_id = $certificate->id;
        $key->key = Crypt::encryptString($encryptionKey);
        $key->save();
    }
}
