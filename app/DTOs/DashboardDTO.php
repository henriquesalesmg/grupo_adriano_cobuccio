<?php

namespace App\DTOs;

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\ReversalRequest;
use App\Models\Activity;
use Illuminate\Support\Collection;

class DashboardDTO
{
    public function __construct(
        public readonly User $user,
        public readonly float $saldo,
        public readonly int $totalTransacoes,
        public readonly int $totalTransferencias,
        public readonly int $totalReversoes,
        public readonly Collection $reversalRequests,
        public readonly ?Activity $ultimaAtividade,
        public readonly bool $temPendenciasFinanceiras
    ) {}

    public static function fromUser(User $user): self
    {
        $account = $user->account;
        $saldo = $account ? $account->saldo : 0;

        $totalTransacoes = Transaction::where('user_id', $user->id)->count();
        $totalTransferencias = Transaction::where('user_id', $user->id)
            ->where('type', 'transfer')
            ->count();
        $totalReversoes = ReversalRequest::where('requester_id', $user->id)->count();

        $reversalRequests = ReversalRequest::with('transaction', 'requester')
            ->where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->get();

        $ultimaAtividade = Activity::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $temPendenciasFinanceiras = $reversalRequests->isNotEmpty();

        return new self(
            user: $user,
            saldo: $saldo,
            totalTransacoes: $totalTransacoes,
            totalTransferencias: $totalTransferencias,
            totalReversoes: $totalReversoes,
            reversalRequests: $reversalRequests,
            ultimaAtividade: $ultimaAtividade,
            temPendenciasFinanceiras: $temPendenciasFinanceiras
        );
    }

    public function getFormattedSaldo(): string
    {
        return number_format($this->saldo, 2, ',', '.');
    }

    public function getSaldoStatus(): string
    {
        if ($this->saldo > 1000) {
            return 'success';
        } elseif ($this->saldo > 100) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    public function getSaldoIcon(): string
    {
        return match($this->getSaldoStatus()) {
            'success' => 'fas fa-arrow-up',
            'warning' => 'fas fa-minus',
            'danger' => 'fas fa-arrow-down',
            default => 'fas fa-wallet'
        };
    }

    public function hasPendingReversals(): bool
    {
        return $this->reversalRequests->isNotEmpty();
    }

    public function getPendingReversalsCount(): int
    {
        return $this->reversalRequests->count();
    }

    public function getWelcomeMessage(): string
    {
        $timeOfDay = now()->format('H');
        $greeting = match(true) {
            $timeOfDay >= 6 && $timeOfDay < 12 => 'Bom dia',
            $timeOfDay >= 12 && $timeOfDay < 18 => 'Boa tarde',
            default => 'Boa noite'
        };

        return $greeting . ', ' . $this->user->name . '!';
    }

    public function getAccountInfo(): array
    {
        $account = $this->user->account;
        
        if (!$account) {
            return [
                'numero' => 'N/A',
                'agencia' => 'N/A',
                'tipo' => 'N/A'
            ];
        }

        return [
            'numero' => $account->numero,
            'agencia' => $account->agencia,
            'tipo' => ucfirst($account->tipo)
        ];
    }

    public function getLastActivityDescription(): ?string
    {
        if (!$this->ultimaAtividade) {
            return null;
        }

        return $this->ultimaAtividade->description;
    }

    public function getLastActivityTime(): ?string
    {
        if (!$this->ultimaAtividade) {
            return null;
        }

        return $this->ultimaAtividade->created_at->diffForHumans();
    }

    public function getTransactionsSummary(): array
    {
        return [
            'total' => $this->totalTransacoes,
            'transferencias' => $this->totalTransferencias,
            'reversoes' => $this->totalReversoes,
        ];
    }

    public function getFinancialHealth(): string
    {
        if ($this->saldo > 5000) {
            return 'Excelente';
        } elseif ($this->saldo > 1000) {
            return 'Boa';
        } elseif ($this->saldo > 100) {
            return 'Moderada';
        } else {
            return 'Atenção necessária';
        }
    }

    public function toArray(): array
    {
        return [
            'user' => $this->user->toArray(),
            'saldo' => $this->saldo,
            'saldo_formatado' => $this->getFormattedSaldo(),
            'total_transacoes' => $this->totalTransacoes,
            'total_transferencias' => $this->totalTransferencias,
            'total_reversoes' => $this->totalReversoes,
            'reversals_pendentes' => $this->reversalRequests->count(),
            'ultima_atividade' => $this->ultimaAtividade?->toArray(),
            'tem_pendencias' => $this->temPendenciasFinanceiras,
            'conta_info' => $this->getAccountInfo(),
            'welcome_message' => $this->getWelcomeMessage(),
            'financial_health' => $this->getFinancialHealth(),
        ];
    }
}