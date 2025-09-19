<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class ReceiptService
{
    public function getTransactionForReceipt($id, $userId)
    {
        return Transaction::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function getReceiptData($transaction, $user)
    {
        if ($transaction->type === 'credit') {
            $operacao = 'Depósito';
        } elseif ($transaction->type === 'debit') {
            $operacao = 'Saque';
        } else {
            $operacao = 'Transferência';
        }
        return [
            'nome' => $user->name,
            'valor' => $transaction->amount,
            'data' => $transaction->executed_at,
            'operacao' => $operacao,
            'destino_tipo' => $transaction->destino_tipo ?? null,
            'destino' => $transaction->destino ?? null,
        ];
    }
}
