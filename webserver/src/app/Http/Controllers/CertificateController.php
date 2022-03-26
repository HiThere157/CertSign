<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    public function certificates_add(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'valid_from' => 'required',
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
        $certificate->valid_from = $request->input('valid_from');
        $certificate->valid_to = $request->input('valid_to');
        $certificate->issuer_id = $request->input('issuer');
        $certificate->serial_number = rand(100000, 999999);
        $certificate->self_signed = $request->input('self_signed') == 'on';
        $certificate->save();

        return redirect('certificates');
    }

    private function generateCSR(){
        $attributes = array(
            "countryName" => "GB",
            "stateOrProvinceName" => "Somerset",
            "localityName" => "Glastonbury",
            "organizationName" => "The Brain Room Limited",
            "organizationalUnitName" => "PHP Documentation Team",
            "commonName" => "Wez Furlong",
            "emailAddress" => "wez@example.com"
        );
        
        $privateKey = openssl_pkey_new(array(
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ));
        openssl_pkey_export($privateKey, $pkeyout, "mypassword") and var_dump($pkeyout);
        
        // Generate a certificate signing request
        $csr = openssl_csr_new($attributes, $privateKey, array('digest_alg' => 'sha256'));
        openssl_csr_export($csr, $csrout) and var_dump($csrout);
        
        // Generate a self-signed cert, valid for 365 days
        $x509 = openssl_csr_sign($csr, null, $privateKey, $days=365, array('digest_alg' => 'sha256'));
        openssl_x509_export($x509, $certout) and var_dump($certout);
        
        // Show any errors that occurred here
        while (($e = openssl_error_string()) !== false) {
            echo $e . "\n";
        }
    }
}
