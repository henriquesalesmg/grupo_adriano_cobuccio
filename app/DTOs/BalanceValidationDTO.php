<?php

namespace App\DTOs;

use App\Models\Account;
use App\Models\Transaction;

class BalanceValidationDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly float $requestedAmount,
        public readonly float $currentBalance,
        public readonly string $operationType
    ) {}

    public static function forUser(int $userId, float $requestedAmount, string $operationType): self
    {
        // Atualiza saldo antes de validar
        Account::atualizarSaldoPorUsuario($userId);

        $account = Account::where('user_id', $userId)->first();
        $currentBalance = $account ? $account->saldo : 0;

        return new self(
            userId: $userId,
            requestedAmount: $requestedAmount,
            currentBalance: $currentBalance,
            operationType: $operationType
        );
    }

    public function hasSufficientBalance(): bool
    {
        return $this->currentBalance >= $this->requestedAmount;
    }

    public function getBalanceDeficit(): float
    {
        return max(0, $this->requestedAmount - $this->currentBalance);
    }

    public function getFormattedBalance(): string
    {
        return number_format($this->currentBalance, 2, ',', '.');
    }

    public function getFormattedRequestedAmount(): string
    {
        return number_format($this->requestedAmount, 2, ',', '.');
    }

    public function getFormattedDeficit(): string
    {
        return number_format($this->getBalanceDeficit(), 2, ',', '.');
    }

    public function getInsufficientBalanceMessage(): string
    {
        return match($this->operationType) {
            'transfer' => 'Saldo insuficiente para transferência.',
            'withdraw' => 'Saldo insuficiente na conta bancária.',
            'reversal' => 'Saldo insuficiente para reversão.',
            default => 'Saldo insuficiente para realizar esta operação.'
        };
    }

    public function validateBalance(): array
    {
        if ($this->hasSufficientBalance()) {
            return [
                'valid' => true,
                'message' => null
            ];
        }

        return [
            'valid' => false,
            'message' => $this->getInsufficientBalanceMessage()
        ];
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'requested_amount' => $this->requestedAmount,
            'current_balance' => $this->currentBalance,
            'operation_type' => $this->operationType,
            'has_sufficient_balance' => $this->hasSufficientBalance(),
            'balance_deficit' => $this->getBalanceDeficit(),
        ];
    }
}
