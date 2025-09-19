<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class ReportService
{
    public function getTransactionsForReport($filters)
    {
        $userId = Auth::id();
        $query = Transaction::where('user_id', $userId);
        if (!empty($filters['data_inicio'])) {
            $query->whereDate('executed_at', '>=', $filters['data_inicio']);
        }
        if (!empty($filters['data_fim'])) {
            $query->whereDate('executed_at', '<=', $filters['data_fim']);
        }
        if (!empty($filters['tipo'])) {
            $query->where('type', $filters['tipo']);
        }
        return $query->orderBy('executed_at', 'desc')->get();
    }

    public function getDashboardTransactions()
    {
        $userId = Auth::id();
        $mesAtual = now()->format('Y-m');
        return Transaction::where('user_id', $userId)
            ->where(function($query) use ($mesAtual) {
                $query->whereRaw('DATE_FORMAT(executed_at, "%Y-%m") >= ?', [$mesAtual]);
            })
            ->orderBy('executed_at', 'desc')
            ->get();
    }
}
