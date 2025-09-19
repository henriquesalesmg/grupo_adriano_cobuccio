<?php

namespace App\DTOs;

class FinancialOperationDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly float $amount,
        public readonly string $type,
        public readonly ?string $description = null,
        public readonly ?string $executedAt = null
    ) {}

    public static function createDeposit(int $userId, float $amount, ?string $description = null): self
    {
        return new self(
            userId: $userId,
            amount: $amount,
            type: 'credit',
            description: $description ?? 'Depósito',
            executedAt: now()->toDateString()
        );
    }

    public static function createWithdraw(int $userId, float $amount, ?string $description = null): self
    {
        return new self(
            userId: $userId,
            amount: $amount,
            type: 'debit',
            description: $description ?? 'Saque',
            executedAt: now()->toDateString()
        );
    }

    public static function fromRequest(array $data, int $userId, string $operationType): self
    {
        $type = match($operationType) {
            'deposit' => 'credit',
            'withdraw' => 'debit',
            default => throw new \InvalidArgumentException('Tipo de operação inválido')
        };

        $description = match($operationType) {
            'deposit' => 'Depósito',
            'withdraw' => 'Saque',
            default => 'Operação financeira'
        };

        $amount = $data['amount'];
        if (is_string($amount)) {
            $amount = str_replace(',', '.', $amount);
            $amount = floatval($amount);
        }
        return new self(
            userId: $userId,
            amount: $amount,
            type: $type,
            description: $data['description'] ?? $description,
            executedAt: $data['executed_at'] ?? now()->toDateString()
        );
    }

    public function getTransactionData(): array
    {
        return [
            'user_id' => $this->userId,
            'amount' => $this->amount,
            'description' => $this->description,
            'executed_at' => $this->executedAt,
            'type' => $this->type,
            'category_transaction_id' => null,
        ];
    }

    public function isDeposit(): bool
    {
        return $this->type === 'credit';
    }

    public function isWithdraw(): bool
    {
        return $this->type === 'debit';
    }

    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 2, ',', '.');
    }

    public function getOperationLabel(): string
    {
        return match($this->type) {
            'credit' => 'Depósito',
            'debit' => 'Saque',
            default => 'Operação'
        };
    }

    public function getActivityDescription(): string
    {
        $operation = $this->getOperationLabel();
        return 'Usuário realizou um ' . strtolower($operation) . ' de R$ ' . $this->getFormattedAmount();
    }

    public function getSuccessMessage(): string
    {
        $operation = $this->getOperationLabel();
        return $operation . ' realizado com sucesso!';
    }

    public function getReceiptType(): string
    {
        return $this->type; // 'credit' para depósito, 'debit' para saque
    }

    public function validateAmount(): bool
    {
        return $this->amount > 0;
    }
}
