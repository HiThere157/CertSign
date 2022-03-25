<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function index()
    {
        return view('pages.login');
    }

    public function login(Request $request)
    {
        $credentials = $this->validate($request, [
            'username' => 'required|string|min:6|max:255',
            'password' => 'required|string|min:6',
        ]);

        if(Auth::attempt($credentials, $request->has('stayLoggedIn'))) {
            $request->session()->regenerate();

            return redirect()->intended();
        } 

        return back()->withErrors([
            'username' => 'The credentials you entered did not match our records. Please try again.',
        ]);
    }

    public function logout()
    {
        auth()->logout();

        return redirect('/');
    }
    
}
