<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
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

    //GET: index page of settings
    public function settings_index()
    {
        if(Gate::allows('isAdmin')) {
            Log::info('[SessionController@index_settings] User ' . Auth::user()->username . ' opended settings.');
            return view('pages.settings', ['users' => User::withTrashed()->get()]);
        }
       
        return redirect()->route('home');
    }

    //GET: disable a user
    public function disable($id)
    {
        if(Auth::user()->id == $id) {
            return back()->withErrors([
                'error' => 'You cannot disable yourself.'
            ]);
        }

        if(Gate::allows('isAdmin')) {
            $user = User::find($id);
            if($user){
                Log::info('[SessionController@disable] User ' . Auth::user()->username . ' disabled user ' . $user->username . '.');
                $user->delete();
            }

            return redirect()->route('settings');
        }
       
        return back()->withErrors([
            'error' => 'You are not allowed to disable users.'
        ]);
    }

    //GET: enable a user
    public function enable($id)
    {
        if(Gate::allows('isAdmin')) {
            $user = User::withTrashed()->find($id);
            if($user){
                Log::info('[SessionController@enable] User ' . Auth::user()->username . ' enabled user ' . $user->username . '.');
                $user->restore();
            }

            return redirect()->route('settings');
        }
       
        return back()->withErrors([
            'error' => 'You are not allowed to enable users.'
        ]);
    }

    //GET: promote a user to admin
    public function promote($id)
    {
        if(Gate::allows('isAdmin')) {
            $user = User::find($id);
            if($user){
                Log::info('[SessionController@promote] User ' . Auth::user()->username . ' promoted user ' . $user->username . ' to admin.');
                $user->is_admin = true;
                $user->save();
            }

            return redirect()->route('settings');
        }
       
        return back()->withErrors([
            'error' => 'You are not allowed to promote users.'
        ]);
    }

    //GET: demote a user from admin
    public function demote($id)
    {
        if(Gate::allows('isAdmin')) {
            $user = User::find($id);
            if($user){
                Log::info('[SessionController@demote] User ' . Auth::user()->username . ' demoted user ' . $user->username . ' from admin.');
                $user->is_admin = false;
                $user->save();
            }

            return redirect()->route('settings');
        }
       
        return back()->withErrors([
            'error' => 'You are not allowed to demote users.'
        ]);
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
