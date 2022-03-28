<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use App\Models\Certificate;
use App\Models\EncryptionKey;

class CertificateController extends Controller
{
    public function index()
    {
        return view('pages.certificates', [
            'root_certificates' => Certificate::all()->where('self_signed', true),
            'certificates' => Certificate::all()->where('self_signed', false)
        ]);
    }

    public function view($id)
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

    public function delete($id)
    {
        $certificate = Certificate::find($id);
        if ($certificate) {
            if(!Gate::allows('owns-cert', $certificate)) {
                return redirect()->route('certificates')->withErrors([
                    'error' => 'No Permission! Contact the owner of this certificate to delete it.'
                ]);
            }

            $certificate->encryptionKey->delete();
            $certificate->delete();
        }

        return redirect()->route('certificates');
    }

    public function changeOwner(Request $request, $id)
    {
        $this->validate($request, [
            'newOwner' => 'required'
        ]);

        $certificate = Certificate::find($id);
        if($certificate){
            if(!(Gate::allows('isAdmin') || Gate::allows('owns-cert', $certificate))){
                return redirect()->route('certificates')->withErrors([
                    'error' => 'No Permission! Only the owner of this certificate can change the owner.'
                ]);
            }

            $certificate->created_by_id_original = $certificate->created_by_id;
            $certificate->created_by_id = $request->input('newOwner');
            $certificate->save();
        }

        return redirect()->route('certificates');
    }

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

        $certificate = new Certificate();
        $certificate->name = $request->input('name');
        $certificate->created_by_id = auth()->user()->id;
        $certificate->valid_from = date("Y-m-d");
        $certificate->valid_to = $request->input('valid_to');
        $certificate->issuer_id = $request->input('issuer');
        $certificate->serial_number = $this->generateNewSerial();
        $certificate->self_signed = $request->input('self_signed') == 'on';
        $certificate->save();

        $this->generateNewCertificate($certificate, $request->input('san'));

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
        for ($i = 0; $i < 20; $i++) {
            $randstring .= $characters[rand(0, strlen($characters)-1)];
        }
        return $randstring;
    }

    private function generateNewCertificate(Certificate $certificate, $subjectAltNames)
    {
        $storagePath = 'certificates/' . dechex($certificate->serial_number);
        $confPath = storage_path('app/' . $storagePath . '/openssl.cnf');

        $configContent = Blade::render(Storage::disk('local')->get('certificates\openssl.blade.cnf'), [
            'commonName' => $certificate->name,
            'created_by' => $certificate->user->username,
            'subjects' => $subjectAltNames ?? [],
            'ca' => $certificate->self_signed ? 'TRUE' : 'FALSE',
        ]);
        Storage::disk('local')->put($storagePath . '/openssl.cnf', $configContent);
                
        $privateKey = openssl_pkey_new([
            'config' => $confPath,
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        
        $csr = openssl_csr_new(['commonName' => $certificate->name, 'organizationalUnitName' => $certificate->user->username], $privateKey, [
            'config' => $confPath,
            'digest_alg' => 'sha256'
        ]);
        
        if(!$certificate->self_signed) {
            $issuerStoragePath = 'certificates/' . dechex(Certificate::find($certificate->issuer_id)->serial_number);
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
