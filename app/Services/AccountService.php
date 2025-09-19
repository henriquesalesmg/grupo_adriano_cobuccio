<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;

class AccountService
{
    public static function atualizarSaldoPorUsuario($userId)
    {
        $hoje = now()->toDateString();
        $saldos = Transaction::where('user_id', $userId)
            ->where('reverted', false)
            ->whereDate('executed_at', '<=', $hoje)
            ->selectRaw('SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as total_credit, SUM(CASE WHEN type = "debit" OR type = "transfer" THEN amount ELSE 0 END) as total_debit')
            ->first();
        $saldo = ($saldos->total_credit ?? 0) - ($saldos->total_debit ?? 0);
        $conta = Account::where('user_id', $userId)->first();
        if ($conta) {
            $conta->saldo = $saldo;
            $conta->save();
        }
    }
}
