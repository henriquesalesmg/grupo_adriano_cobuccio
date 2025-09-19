<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Account;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $cpf = preg_replace('/[^0-9]/', '', $validated['cpf']);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'cpf' => $cpf,
            'birthdate' => $validated['birthdate'],
            'security_answer' => $validated['security_answer'],
            'password' => Hash::make($validated['password']),
        ]);

        // Gerar número de conta único de 4 dígitos
        do {
            $numeroConta = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (Account::where('numero', $numeroConta)->exists());

        Account::create([
            'user_id' => $user->id,
            'numero' => $numeroConta,
            'agencia' => '0001',
            'tipo' => 'corrente',
            'saldo' => 0,
            'status' => 'ativa',
            'data_abertura' => now()->toDateString(),
            'titular' => $user->name,
            'documento' => $user->cpf,
            'banco' => 'Banco Exemplo',
        ]);

        Auth::login($user);
        return redirect('/dashboard');
    }
}
