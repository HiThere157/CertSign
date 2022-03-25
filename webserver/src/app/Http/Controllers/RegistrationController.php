<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class RegistrationController extends Controller
{
    public function index()
    {
        return view('pages.register');
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string|min:6|max:255|unique:users',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = new User;
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->save();

        auth()->login($user);

        return redirect('/');
    }
}
