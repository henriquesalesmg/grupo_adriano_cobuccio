<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:receita,despesa',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser numérico.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'description.required' => 'A descrição é obrigatória.',
            'description.max' => 'A descrição deve ter no máximo 255 caracteres.',
            'date.required' => 'A data é obrigatória.',
            'date.date' => 'A data deve ser válida.',
            'type.required' => 'O tipo é obrigatório.',
            'type.in' => 'O tipo deve ser receita ou despesa.'
        ];
    }
}
