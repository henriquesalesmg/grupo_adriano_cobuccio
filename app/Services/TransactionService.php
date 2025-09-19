<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Http\Classes\Activities;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    public function create(array $validated, $request)
    {
        $amount = $validated['amount'];
        if (is_string($amount)) {
            $amount = str_replace(',', '.', $amount);
            $amount = floatval($amount);
        }
        // Obter saldo atual via AccountService
        $conta = \App\Models\Account::where('user_id', Auth::id())->first();
        $saldoAtual = $conta ? $conta->saldo : 0;
        $type = $validated['type'] === 'receita' ? 'credit' : 'debit';
        if ($type === 'debit' && $amount > $saldoAtual) {
            return ['error' => 'Saldo insuficiente para realizar esta operação.'];
        }
        $categoriaNome = $request->input('categoria');
        $categoriaId = null;
        if ($categoriaNome) {
            $categoria = \App\Services\TransactionCategoryService::buscarOuCriar($categoriaNome);
            $categoriaId = $categoria->id;
        }
        Log::info('DEBUG TYPE VALUE', context: ['original' => $validated['type'], 'converted' => $type]);
        $trans = Transaction::create([
            'user_id' => Auth::id(),
            'amount' => $amount,
            'description' => $validated['description'],
            'executed_at' => $validated['executed_at'],
            'type' => $type,
            'category_transaction_id' => $categoriaId,
        ]);
        Activities::build('Usuário cadastrou uma movimentação (' . $trans->description . ') no valor de R$ ' . number_format($trans->amount, 2, ',', '.') . '.');
        \App\Services\AccountService::atualizarSaldoPorUsuario(Auth::id());
        return ['success' => 'Movimentação cadastrada com sucesso!'];
    }

    public function update($id, $request)
    {
    $transaction = \App\Models\Transaction::where('user_id', Auth::id())->findOrFail($id);
        if (!in_array($transaction->type, ['credit', 'debit'])) {
            return ['error' => 'Apenas receitas ou despesas podem ser editadas.'];
        }
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required',
            'executed_at' => 'required|date',
            'category_transaction_id' => 'nullable|exists:transaction_categories,id',
        ]);
        $amount = $validated['amount'];
        if (is_string($amount)) {
            $amount = str_replace(['.', ','], ['', '.'], $amount);
            $amount = floatval($amount);
        }
        $transaction->description = $validated['description'];
        $transaction->amount = $amount;
        $transaction->executed_at = $validated['executed_at'];
        $transaction->category_transaction_id = $validated['category_transaction_id'] ?? null;
        $transaction->save();
        \App\Http\Classes\Activities::build('Usuário editou a transação #' . $transaction->id . ' (' . $transaction->description . ') para o valor de R$ ' . number_format($transaction->amount, 2, ',', '.'));
        return ['success' => 'Transação atualizada com sucesso!'];
    }

    public function destroy($id)
    {
    $transaction = \App\Models\Transaction::where('user_id', Auth::id())->findOrFail($id);
        if (
            ($transaction->type === 'credit' || $transaction->type === 'debit') &&
            ($transaction->description === 'Reversão de transferência (Operação revertida)' || $transaction->description === 'Depósito')
        ) {
            return ['error' => 'Esta transação não pode ser excluída.'];
        }
        $transaction->delete();
        \App\Http\Classes\Activities::build('Usuário excluiu a transação #' . $transaction->id . ' (' . $transaction->description . ')');
        return ['success' => 'Transação excluída com sucesso!'];
    }

    public function revert($id)
    {
    $transaction = \App\Models\Transaction::where('user_id', Auth::id())->findOrFail($id);
        if ($transaction->type !== 'transfer') {
            return ['error' => 'Apenas transferências podem ser revertidas.'];
        }
        if ($transaction->reverted) {
            return ['error' => 'Esta transferência já foi revertida.'];
        }
        // Aqui pode-se implementar lógica de reversão, se necessário
        return ['success' => 'Solicitação de reversão enviada ao destinatário. Aguarde a autorização.'];
    }

    public function getIndexData()
    {
    $userId = Auth::id();
        $query = \App\Models\Transaction::where('user_id', $userId);

        $dataInicio = request('data_inicio');
        $dataFim = request('data_fim');
        if ($dataInicio) {
            $query->whereDate('executed_at', '>=', $dataInicio);
        }
        if ($dataFim) {
            $query->whereDate('executed_at', '<=', $dataFim);
        }

        $categoriaFiltro = request('categoria_filtro');
        if ($categoriaFiltro) {
            $query->whereHas('category', function($q) use ($categoriaFiltro) {
                $q->where('name', $categoriaFiltro);
            });
        }

        $tipoFiltro = request('tipo_filtro');
        if ($tipoFiltro) {
            $query->where('type', $tipoFiltro);
        }

        $transactions = $query->orderByDesc('executed_at')->get();
        $categorias = \App\Models\TransactionCategory::orderBy('name')->get();

        $hoje = now()->toDateString();

        $saldoAtualData = \App\Models\Transaction::where('user_id', $userId)
            ->whereDate('executed_at', '<=', $hoje)
            ->where('reverted', false)
            ->selectRaw('SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as total_credit, SUM(CASE WHEN type = "debit" OR type = "transfer" THEN amount ELSE 0 END) as total_debit')
            ->first();
        $saldoAtual = ($saldoAtualData->total_credit ?? 0) - ($saldoAtualData->total_debit ?? 0);

        $valoresReceber = \App\Models\Transaction::where('user_id', $userId)
            ->where('type', 'credit')
            ->where('reverted', false)
            ->whereDate('executed_at', '>', $hoje)
            ->sum('amount');

        $contasPagar = \App\Models\Transaction::where('user_id', $userId)
            ->where('type', 'debit')
            ->where('reverted', false)
            ->whereDate('executed_at', '>', $hoje)
            ->sum('amount');

        $saldoFuturo = $saldoAtual + $valoresReceber - $contasPagar;

    $user = Auth::user();
        $reversalRequests = \App\Models\ReversalRequest::with('transaction', 'requester', 'receiver')
            ->where('receiver_id', $userId)
            ->where('status', 'pending')
            ->get();

        $receivedTransfers = collect();
        $lastLogin = session('last_login_at');
        if ($lastLogin) {
            $loginTime = \Carbon\Carbon::parse($lastLogin);
            $limitTime = $loginTime->copy()->addMinutes(10);
            $receivedTransfers = \App\Models\Transaction::where('user_id', $userId)
                ->where('type', 'credit')
                ->where('description', 'like', 'Recebido de transferência%')
                ->where('created_at', '>=', $loginTime)
                ->where('created_at', '<=', $limitTime)
                ->orderByDesc('executed_at')
                ->get();
        }

        return compact('transactions', 'categorias', 'saldoAtual', 'valoresReceber', 'contasPagar', 'saldoFuturo', 'user', 'reversalRequests', 'receivedTransfers');
    }
}
