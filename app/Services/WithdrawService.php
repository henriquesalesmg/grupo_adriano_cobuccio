<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Account;
use App\DTOs\FinancialOperationDTO;
use App\Http\Classes\Activities;

class WithdrawService
{
    public function create(array $validated, int $userId)
    {
        $operationDto = FinancialOperationDTO::fromRequest($validated, $userId, 'withdraw');
        \App\Services\AccountService::atualizarSaldoPorUsuario($userId);
        $conta = Account::where('user_id', $userId)->first();
        if (!$conta) {
            return ['error' => 'Conta bancária não encontrada.'];
        }
        $conta->refresh();
        if ($conta->saldo < $operationDto->amount) {
            return ['error' => 'Saldo insuficiente na conta bancária.'];
        }
        $transaction = Transaction::create($operationDto->getTransactionData());
        Activities::build($operationDto->getActivityDescription());
        \App\Services\AccountService::atualizarSaldoPorUsuario($userId);
        return [
            'transaction' => $transaction,
            'success' => $operationDto->getSuccessMessage(),
            'receipt_id' => $transaction->id,
            'receipt_type' => $operationDto->getReceiptType(),
        ];
    }
}
