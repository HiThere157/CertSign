<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function rootCa_index()
    {
        return view('pages.root-ca');
    }

    public function certificates_index()
    {
        return view('pages.certificates');
    }
}
