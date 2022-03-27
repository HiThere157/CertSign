<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Blade;
use App\Models\Certificate;

class CertificateController extends Controller
{
    public function certificates_index()
    {
        return view('pages.certificates', [
            'root_certificates' => Certificate::all()->where('self_signed', true),
            'certificates' => Certificate::all()->where('self_signed', false)
        ]);
    }

    public function certificates_delete($id)
    {
        $certificate = Certificate::find($id);
        if ($certificate) {
            $certificate->delete();
        }
        return redirect('certificates');
    }

    public function certificates_add(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'valid_to' => 'required'
        ]);

        if($request->input('issuer') == '' && $request->input('self_signed') != 'on') {
            return redirect()->back()->withErrors([
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

        $this->generateNewCertificate($certificate);

        return redirect('certificates');
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

    private function generateNewCertificate(Certificate $certificate){
        $storagePath = 'certificates/' . dechex($certificate->serial_number);
        $confPath = storage_path('app/' . $storagePath . '/openssl.cnf');

        $configContent = Blade::render(Storage::disk('local')->get('certificates\openssl.blade.cnf'), [
            'commonName' => $certificate->name,
            'created_by' => $certificate->user->username,
            'subjects' => ['test.com', 'test2.com'],
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
            $issuerPrivateKey = openssl_pkey_get_private(Storage::disk('local')->get($issuerStoragePath . '/cert.key'), 'mypassword');
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

        openssl_pkey_export($privateKey, $privateKeyOut, 'mypassword', ['config' => $confPath]);
        openssl_csr_export($csr, $csrOut);
        openssl_x509_export($x509, $certOut);

        Storage::disk('local')->put($storagePath . '/cert.key', $privateKeyOut);
        Storage::disk('local')->put($storagePath . '/cert.csr', $csrOut);
        Storage::disk('local')->put($storagePath . '/cert.cer', $certOut);
    }
}
