<?php

namespace App\Services;

use App\Models\Transaction;
use App\DTOs\FinancialOperationDTO;
use App\Http\Classes\Activities;

class DepositService
{
    public function create(array $validated, int $userId)
    {
        $operationDto = FinancialOperationDTO::fromRequest($validated, $userId, 'deposit');
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
