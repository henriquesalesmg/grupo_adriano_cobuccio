<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Account;

class HistoricalService
{
    public function getTransfers($userId, $filters = [])
    {
        $query = Transaction::where('user_id', $userId)
            ->where('type', 'transfer');

        if (!empty($filters['data_inicio'])) {
            $query->whereDate('created_at', '>=', $filters['data_inicio']);
        }
        if (!empty($filters['data_fim'])) {
            $query->whereDate('created_at', '<=', $filters['data_fim']);
        }

        $transfers = $query->orderByDesc('created_at')->get()->map(function($transfer) {
            $destinoConta = Account::where('numero', $transfer->destino)->first();
            $transfer->destinatario_nome = $destinoConta ? ($destinoConta->user->name ?? '-') : '-';
            $transfer->destinatario_agencia = $destinoConta ? $destinoConta->agencia : '-';
            $transfer->destinatario_conta = $destinoConta ? $destinoConta->numero : '-';
            return $transfer;
        });

        return $transfers;
    }
}
