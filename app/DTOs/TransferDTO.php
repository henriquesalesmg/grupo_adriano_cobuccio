<?php

namespace App\DTOs;

class TransferDTO
{
    public function __construct(
        public readonly int $userIdOrigem,
        public readonly int $userIdDestino,
        public readonly float $amount,
        public readonly string $numeroContaOrigem,
        public readonly string $numeroContaDestino,
        public readonly string $executedAt,
        public readonly string $password,
        public readonly ?string $descricaoOrigem = null,
        public readonly ?string $descricaoDestino = null
    ) {}

    public static function fromRequest(array $data, int $userIdOrigem, string $numeroContaOrigem): self
    {
    $amountStr = strval($data['amount']);
    $amountStr = str_replace(',', '.', $amountStr);
    $amount = floatval($amountStr);

        return new self(
            userIdOrigem: $userIdOrigem,
            userIdDestino: 0, // Será definido após validação da conta destino
            amount: $amount,
            numeroContaOrigem: $numeroContaOrigem,
            numeroContaDestino: $data['numero_conta'],
            executedAt: $data['executed_at'] ?? now()->toDateString(),
            password: $data['password'],
            descricaoOrigem: $data['descricao_origem'] ?? null,
            descricaoDestino: $data['descricao_destino'] ?? null
        );
    }

    public function withUserIdDestino(int $userIdDestino): self
    {
        return new self(
            userIdOrigem: $this->userIdOrigem,
            userIdDestino: $userIdDestino,
            amount: $this->amount,
            numeroContaOrigem: $this->numeroContaOrigem,
            numeroContaDestino: $this->numeroContaDestino,
            executedAt: $this->executedAt,
            password: $this->password,
            descricaoOrigem: $this->descricaoOrigem,
            descricaoDestino: $this->descricaoDestino
        );
    }

    public function getTransactionDataOrigem(): array
    {
        return [
            'user_id' => $this->userIdOrigem,
            'amount' => $this->amount,
            'description' => $this->descricaoOrigem ?? 'Transferência para conta ' . $this->numeroContaDestino,
            'executed_at' => $this->executedAt,
            'type' => 'transfer',
            'category_transaction_id' => null,
            'destino_tipo' => 'conta',
            'destino' => $this->numeroContaDestino,
        ];
    }

    public function getTransactionDataDestino(): array
    {
        return [
            'user_id' => $this->userIdDestino,
            'amount' => $this->amount,
            'description' => $this->descricaoDestino ?? 'Recebido de transferência da conta ' . $this->numeroContaOrigem,
            'executed_at' => $this->executedAt,
            'type' => 'credit',
            'category_transaction_id' => null,
            'destino_tipo' => 'conta',
            'destino' => $this->numeroContaOrigem,
        ];
    }

    public function isValidAmount(): bool
    {
        return $this->amount > 0;
    }

    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 2, ',', '.');
    }

    public function getActivityDescription(): string
    {
        return 'Usuário realizou uma transferência de R$ ' . $this->getFormattedAmount() .
               ' para a conta ' . $this->numeroContaDestino;
    }
}
