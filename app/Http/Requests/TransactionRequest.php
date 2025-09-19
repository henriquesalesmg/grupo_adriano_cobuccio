<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('amount')) {
            $amount = trim($this->input('amount'));
            $amount = preg_replace('/[^\d,.]/', '', $amount);
            if (empty($amount)) {
                $this->merge(['amount' => '']);
                return;
            }
            if (strpos($amount, ',') !== false) {
                // Se tem vírgula, é separador decimal. Remove pontos (milhar) e troca vírgula por ponto
                $amount = str_replace('.', '', $amount);
                $amount = str_replace(',', '.', $amount);
            } elseif (strpos($amount, '.') !== false) {
                // Se só tem ponto, trata como decimal (compatibilidade)
                // Ex: 12.34 -> 12.34
                // Remove vírgulas só por segurança
                $amount = str_replace(',', '', $amount);
            } else {
                // Sem separador: assume últimos 2 dígitos como centavos
                if (strlen($amount) >= 3) {
                    $amount = substr($amount, 0, -2) . '.' . substr($amount, -2);
                } else {
                    $amount = '0.' . str_pad($amount, 2, '0', STR_PAD_LEFT);
                }
            }
            $this->merge(['amount' => $amount]);
        }
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'description' => 'required|string|max:255',
            'executed_at' => 'required|date',
            'type' => 'required|in:receita,despesa,credit,debit',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser numérico.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'amount.max' => 'O valor máximo permitido é R$ 999.999,99.',
            'description.required' => 'A descrição é obrigatória.',
            'description.max' => 'A descrição deve ter no máximo 255 caracteres.',
            'executed_at.required' => 'A data é obrigatória.',
            'executed_at.date' => 'A data deve ser válida.',
            'type.required' => 'O tipo é obrigatório.',
            'type.in' => 'O tipo deve ser receita, despesa, credit ou debit.'
        ];
    }
}
