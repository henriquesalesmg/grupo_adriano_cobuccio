<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class ReversalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $transactionId = $this->route('transaction');

        return [
            'transaction_id' => [
                'sometimes',
                'exists:transactions,id',
                function($attribute, $value, $fail) use ($transactionId) {
                    $transaction = Transaction::find($transactionId ?? $value);
                    if (!$transaction) {
                        $fail('Transação não encontrada.');
                        return;
                    }

                    if ($transaction->user_id !== Auth::id()) {
                        $fail('Você só pode solicitar reversão de suas próprias transferências.');
                    }

                    if ($transaction->type !== 'transfer') {
                        $fail('Apenas transferências podem ser revertidas.');
                    }

                    if ($transaction->reverted) {
                        $fail('Esta transferência já foi revertida.');
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_id.exists' => 'Transação não encontrada.',
        ];
    }
}
