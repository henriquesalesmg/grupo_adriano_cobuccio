<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class WithdrawRequest extends FormRequest
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
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
                function($attribute, $value, $fail) {
                    $userId = Auth::id();
                    $saldoAtual = Transaction::where('user_id', $userId)
                        ->whereDate('executed_at', '<=', now()->toDateString())
                        ->where('reverted', false)
                        ->selectRaw('SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as total_credit, SUM(CASE WHEN type = "debit" OR type = "transfer" THEN amount ELSE 0 END) as total_debit')
                        ->first();
                    $saldo = ($saldoAtual->total_credit ?? 0) - ($saldoAtual->total_debit ?? 0);
                    if ($value > $saldo) {
                        $fail('O valor do saque não pode ser maior que o saldo atual (R$ ' . number_format($saldo, 2, ',', '.') . ').');
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser numérico.',
            'amount.min' => 'O valor mínimo para saque é R$ 0,01.',
            'amount.max' => 'O valor máximo para saque é R$ 999.999,99.',
        ];
    }
}
