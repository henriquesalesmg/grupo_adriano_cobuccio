<?php

namespace App\Services;

use App\Models\ReversalRequest;
use App\Models\Transaction;
use App\Models\Account;
use App\Http\Classes\Activities;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReversalRequestService
{
    public function create($transactionId, $user)
    {
        $transaction = Transaction::findOrFail($transactionId);
        if ($transaction->user_id !== $user->id || $transaction->type !== 'transfer') {
            return ['error' => 'Operação não permitida.'];
        }
        if (ReversalRequest::where('transaction_id', $transactionId)->where('status', 'pending')->exists()) {
            return ['error' => 'Já existe uma solicitação de reversão pendente para esta transferência.'];
        }
        $contaDestino = Account::where('numero', $transaction->destino)->first();
        if (!$contaDestino) {
            return ['error' => 'Conta de destino não encontrada.'];
        }
        $reversal = ReversalRequest::create([
            'transaction_id' => $transactionId,
            'requester_id' => $user->id,
            'receiver_id' => $contaDestino->user_id,
            'status' => 'pending',
        ]);
        Log::info('ReversalRequest criada', [
            'id' => $reversal->id,
            'transaction_id' => $reversal->transaction_id,
            'requester_id' => $reversal->requester_id,
            'receiver_id' => $reversal->receiver_id,
            'status' => $reversal->status,
        ]);
        Transaction::create([
            'user_id' => $user->id,
            'amount' => $transaction->amount,
            'description' => 'Solicitação de reversão da transferência para conta ' . $contaDestino->numero,
            'executed_at' => now(),
            'type' => 'transfer',
            'category_transaction_id' => null,
            'destino_tipo' => 'conta',
            'destino' => $contaDestino->numero,
        ]);
        Activities::build('Usuário solicitou reversão da transferência #' . $transactionId . ' para a conta ' . $contaDestino->numero);
        return ['success' => 'Solicitação de reversão enviada ao destinatário.'];
    }

    public function approve($id, $user)
    {
        $reversal = ReversalRequest::findOrFail($id);
        if ($reversal->receiver_id !== $user->id || $reversal->status !== 'pending') {
            return ['error' => 'Operação não permitida.'];
        }
        $transaction = $reversal->transaction;
        $contaOrigem = Account::where('user_id', $reversal->requester_id)->first();
        $contaDestino = Account::where('user_id', $reversal->receiver_id)->first();
        \App\Services\AccountService::atualizarSaldoPorUsuario($contaDestino->user_id);
        $contaDestino->refresh();
        if ($contaDestino->saldo < $transaction->amount) {
            return ['error' => 'Saldo insuficiente para reversão.'];
        }
        Transaction::create([
            'user_id' => $contaDestino->user_id,
            'amount' => $transaction->amount,
            'description' => 'Estorno de transferência para ' . $contaOrigem->numero . ' (Reversão solicitada por ' . $contaOrigem->user->name . ')',
            'executed_at' => now(),
            'type' => 'debit',
            'category_transaction_id' => null,
            'destino_tipo' => 'conta',
            'destino' => $contaOrigem->numero,
        ]);
        Transaction::create([
            'user_id' => $contaOrigem->user_id,
            'amount' => $transaction->amount,
            'description' => 'Recebido de estorno de transferência de ' . $contaDestino->numero . ' (Reversão autorizada)',
            'executed_at' => now(),
            'type' => 'credit',
            'category_transaction_id' => null,
            'destino_tipo' => 'conta',
            'destino' => $contaDestino->numero,
        ]);
        \App\Services\AccountService::atualizarSaldoPorUsuario($contaDestino->user_id);
        \App\Services\AccountService::atualizarSaldoPorUsuario($contaOrigem->user_id);
        $transaction->reverted = true;
        $transaction->save();
        $reversal->status = 'approved';
        $reversal->save();
        Activities::build('Usuário aprovou a reversão da transferência #' . $reversal->transaction_id . ' para ' . $contaOrigem->numero);
        return ['success' => 'Reversão aprovada e valores revertidos.'];
    }

    public function reject($id, $user)
    {
        $reversal = ReversalRequest::findOrFail($id);
        if ($reversal->receiver_id !== $user->id || $reversal->status !== 'pending') {
            return ['error' => 'Operação não permitida.'];
        }
        $reversal->status = 'rejected';
        $reversal->save();
        Activities::build('Usuário rejeitou a reversão da transferência #' . $reversal->transaction_id);
        return ['success' => 'Solicitação de reversão rejeitada.'];
    }
}
