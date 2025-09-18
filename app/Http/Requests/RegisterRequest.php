<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cpf' => ['required', 'string', 'size:14', 'unique:users', function($attribute, $value, $fail) {
                $cpf = preg_replace('/[^0-9]/', '', $value);
                if (strlen($cpf) != 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
                    return $fail('CPF inválido.');
                }
                for ($t = 9; $t < 11; $t++) {
                    for ($d = 0, $c = 0; $c < $t; $c++) {
                        $d += $cpf[$c] * (($t + 1) - $c);
                    }
                    $d = ((10 * $d) % 11) % 10;
                    if ($cpf[$c] != $d) {
                        return $fail('CPF inválido.');
                    }
                }
            }],
            'birthdate' => 'required|date|before:today',
            'security_answer' => 'required|string|max:255',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\d]).+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'cpf.size' => 'O CPF deve estar no formato 000.000.000-00.',
            'cpf.*' => 'CPF inválido.',
            'birthdate.required' => 'A data de nascimento é obrigatória.',
            'birthdate.date' => 'A data de nascimento deve ser uma data válida.',
            'birthdate.before' => 'A data de nascimento deve ser anterior a hoje.',
            'security_answer.required' => 'A resposta de segurança é obrigatória.',
            'password.regex' => 'A senha deve conter pelo menos uma letra maiúscula, uma minúscula, um número e um caractere especial.'
        ];
    }
}
