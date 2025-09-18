<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    public function showVerifyForm()
    {
        return view('auth.passwords.verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'cpf' => 'required',
            'birthdate' => 'required|date',
            'security_answer' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('cpf', $request->cpf)
            ->where('birthdate', $request->birthdate)
            ->where('security_answer', $request->security_answer)
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Dados não conferem.'])->withInput();
        }

        // Passa o email para a tela de redefinição
        return view('auth.passwords.reset_custom', ['email' => $user->email]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).{6,}$/',
            ],
        ], [
            'password.regex' => 'A senha deve ter pelo menos 6 caracteres, incluindo uma letra maiúscula, uma minúscula, um número e um caractere especial.'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Usuário não encontrado.']);
        }
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('status', 'Senha redefinida com sucesso!');
    }
}
