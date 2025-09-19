<?php

namespace App\DTOs;

class TransactionDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly float $amount,
        public readonly string $description,
        public readonly string $executedAt,
        public readonly string $type,
        public readonly ?int $categoryTransactionId = null,
        public readonly ?string $destinoTipo = null,
        public readonly ?string $destino = null,
        public readonly bool $reverted = false
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            amount: $data['amount'],
            description: $data['description'],
            executedAt: $data['executed_at'],
            type: $data['type'],
            categoryTransactionId: $data['category_transaction_id'] ?? null,
            destinoTipo: $data['destino_tipo'] ?? null,
            destino: $data['destino'] ?? null,
            reverted: $data['reverted'] ?? false
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'amount' => $this->amount,
            'description' => $this->description,
            'executed_at' => $this->executedAt,
            'type' => $this->type,
            'category_transaction_id' => $this->categoryTransactionId,
            'destino_tipo' => $this->destinoTipo,
            'destino' => $this->destino,
            'reverted' => $this->reverted,
        ];
    }

    public function isCredit(): bool
    {
        return $this->type === 'credit';
    }

    public function isDebit(): bool
    {
        return $this->type === 'debit';
    }

    public function isTransfer(): bool
    {
        return $this->type === 'transfer';
    }

    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 2, ',', '.');
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'credit' => 'Receita',
            'debit' => 'Despesa',
            'transfer' => 'Transferência',
            default => 'Desconhecido'
        };
    }
}