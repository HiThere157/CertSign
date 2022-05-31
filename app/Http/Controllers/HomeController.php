<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Certificate;
use App\Models\User;

class HomeController extends Controller
{
    //GET: index home page
    public function index()
    {
        return view('pages.home', [
            'expired_certificates' => Certificate::where('valid_to', '<', date('Y-m-d'))->count(),
            'all_certificates' => Certificate::count(),
            'all_users' => User::count(),
        ]);
    }
}
