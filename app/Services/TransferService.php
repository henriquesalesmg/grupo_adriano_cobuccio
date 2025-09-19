<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Classes\Activities;

class TransferService
{
    public function create(array $data, $user)
    {
        $userId = $user->id;
        $executedAt = $data['executed_at'] ?? now()->toDateString();
        $amount = $data['amount'];
        $password = $data['password'] ?? null;

        if (!is_numeric($amount) || $amount <= 0) {
            return ['error' => 'O valor informado é inválido.'];
        }
        if (!Hash::check($password, $user->password)) {
            return ['error' => 'Senha incorreta.'];
        }
        $contaOrigem = Account::where('user_id', $userId)->first();
        if (!$contaOrigem) {
            return ['error' => 'Você não possui conta cadastrada.'];
        }
        \App\Services\AccountService::atualizarSaldoPorUsuario($userId);
        $contaOrigem->refresh();
        if ($contaOrigem->saldo < $amount) {
            return ['error' => 'Saldo insuficiente para transferência.'];
        }
        $contaDestino = Account::where('numero', $data['numero_conta'])->first();
        if (!$contaDestino) {
            return ['error' => 'Conta de destino não encontrada.'];
        }
        if ($contaDestino->user_id == $userId) {
            return ['error' => 'Não é possível transferir para sua própria conta.'];
        }
        Transaction::create([
            'user_id' => $userId,
            'amount' => $amount,
            'description' => 'Transferência para conta ' . $contaDestino->numero,
            'executed_at' => $executedAt,
            'type' => 'transfer',
            'category_transaction_id' => null,
            'destino_tipo' => 'conta',
            'destino' => $contaDestino->numero,
        ]);
        Transaction::create([
            'user_id' => $contaDestino->user_id,
            'amount' => $amount,
            'description' => 'Recebido de transferência da conta ' . $contaOrigem->numero,
            'executed_at' => $executedAt,
            'type' => 'credit',
            'category_transaction_id' => null,
            'destino_tipo' => 'conta',
            'destino' => $contaOrigem->numero,
        ]);
        \App\Services\AccountService::atualizarSaldoPorUsuario($userId);
        \App\Services\AccountService::atualizarSaldoPorUsuario($contaDestino->user_id);
        Activities::build('Usuário realizou uma transferência de R$ ' . number_format($amount, 2, ',', '.') . ' para a conta ' . $contaDestino->numero);
        return ['success' => 'Transferência realizada com sucesso!'];
    }
}
