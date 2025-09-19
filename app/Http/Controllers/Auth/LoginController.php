<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Classes\Activities;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            session(['last_login_at' => now()]);
            $request->session()->regenerate();
            Activities::build('Usuário logou no sistema');
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'As credenciais informadas não conferem.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Activities::build('Usuário deslogou do sistema');
        return redirect('/login');
    }
}
