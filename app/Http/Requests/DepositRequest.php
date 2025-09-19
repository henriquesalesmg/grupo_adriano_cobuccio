<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
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
                $amount = str_replace('.', '', $amount);
                $amount = str_replace(',', '.', $amount);
            } elseif (strpos($amount, '.') !== false) {
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
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser numérico.',
            'amount.min' => 'O valor mínimo para depósito é R$ 0,01.',
            'amount.max' => 'O valor máximo para depósito é R$ 999.999,99.',
        ];
    }
}
