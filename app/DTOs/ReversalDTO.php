<?php

namespace App\DTOs;

use App\Models\Transaction;
use App\Models\Account;

class ReversalDTO
{
    public function __construct(
        public readonly int $transactionId,
        public readonly int $requesterId,
        public readonly int $receiverId,
        public readonly float $amount,
        public readonly string $numeroContaOrigem,
        public readonly string $numeroContaDestino,
        public readonly string $status = 'pending',
        public readonly ?string $reason = null
    ) {}

    public static function fromTransaction(Transaction $transaction, int $requesterId): self
    {
        $contaDestino = Account::where('numero', $transaction->destino)->first();
        $contaOrigem = Account::where('user_id', $requesterId)->first();

        if (!$contaDestino || !$contaOrigem) {
            throw new \InvalidArgumentException('Contas não encontradas para criar reversão');
        }

        return new self(
            transactionId: $transaction->id,
            requesterId: $requesterId,
            receiverId: $contaDestino->user_id,
            amount: $transaction->amount,
            numeroContaOrigem: $contaOrigem->numero,
            numeroContaDestino: $contaDestino->numero
        );
    }

    public function getReversalRequestData(): array
    {
        return [
            'transaction_id' => $this->transactionId,
            'requester_id' => $this->requesterId,
            'receiver_id' => $this->receiverId,
            'status' => $this->status,
        ];
    }

    public function getSymbolicTransactionData(): array
    {
        return [
            'user_id' => $this->requesterId,
            'amount' => $this->amount,
            'description' => 'Solicitação de reversão da transferência para conta ' . $this->numeroContaDestino,
            'executed_at' => now(),
            'type' => 'transfer',
            'category_transaction_id' => null,
            'destino_tipo' => 'conta',
            'destino' => $this->numeroContaDestino,
        ];
    }

    public function getDebitTransactionData(): array
    {
        return [
            'user_id' => $this->receiverId,
            'amount' => $this->amount,
            'description' => 'Estorno de transferência para ' . $this->numeroContaOrigem,
            'executed_at' => now(),
            'type' => 'debit',
            'category_transaction_id' => null,
            'destino_tipo' => 'conta',
            'destino' => $this->numeroContaOrigem,
        ];
    }

    public function getCreditTransactionData(): array
    {
        return [
            'user_id' => $this->requesterId,
            'amount' => $this->amount,
            'description' => 'Reversão de transferência (Operação revertida)',
            'executed_at' => now(),
            'type' => 'credit',
            'category_transaction_id' => null,
            'destino_tipo' => 'conta',
            'destino' => $this->numeroContaDestino,
        ];
    }

    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 2, ',', '.');
    }

    public function getRequestActivityDescription(): string
    {
        return 'Usuário solicitou reversão da transferência #' . $this->transactionId .
               ' para a conta ' . $this->numeroContaDestino;
    }

    public function getApprovalActivityDescription(string $requesterName): string
    {
        return 'Usuário aprovou reversão da transferência de R$ ' . $this->getFormattedAmount() .
               ' solicitada por ' . $requesterName;
    }

    public function getRejectionActivityDescription(string $requesterName): string
    {
        return 'Usuário rejeitou reversão da transferência de R$ ' . $this->getFormattedAmount() .
               ' solicitada por ' . $requesterName;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
