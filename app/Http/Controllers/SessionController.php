<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class SessionController extends Controller
{
    //GET: index page of login
    public function login_index()
    {
        return view('pages.auth.login');
    }

    //GET: index page to reauth password
    public function reauth_index()
    {
        return view('pages.auth.reauth');
    }

    //POST: login with credentials
    public function login(Request $request)
    {
        $credentials = $this->validate($request, [
            'username' => 'required|string|min:6|max:255',
            'password' => 'required|string|min:6',
        ]);

        if(Auth::attempt($credentials, $request->has('stayLoggedIn'))) {
            Log::info('[SessionController@login] User ' . Auth::user()->username . ' logged in.');
            $request->session()->regenerate();

            return redirect()->intended();
        } 

        return back()->withErrors([
            'username' => 'The credentials you entered did not match our records. Please try again.',
        ]);
    }

    //GET: logout
    public function logout()
    {
        Log::info('[SessionController@logout] User ' . Auth::user()->username . ' logged out.');
        auth()->logout();

        return redirect()->route('login');
    }

    //POST: reauth with credentials
    public function reauth(Request $request)
    {
        if (!Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors([
                'password' => ['The provided password does not match our records.']
            ]);
        }
     
        Log::info('[SessionController@reauth] User ' . Auth::user()->username . ' reauthenticated.');
        $request->session()->passwordConfirmed();
     
        return redirect()->intended();
    }
}
