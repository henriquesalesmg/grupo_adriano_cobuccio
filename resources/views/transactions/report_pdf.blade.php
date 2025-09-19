<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Movimentações</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h2>Relatório de Movimentações</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Valor</th>
                <th>Descrição</th>
                <th>Tipo</th>
                <th>Destino</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->executed_at)->format('d/m/Y') }}</td>
                    <td>R$ {{ number_format($transaction->amount, 2, ',', '.') }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>
                        @if ($transaction->type === 'credit')
                            Receita
                        @elseif ($transaction->type === 'debit')
                            Despesa
                        @elseif ($transaction->type === 'transfer')
                            Transferência
                        @endif
                    </td>
                    <td>
                        @if ($transaction->type === 'transfer')
                            @if ($transaction->destino_tipo === 'conta')
                                Conta: {{ $transaction->destino }}
                            @elseif ($transaction->destino_tipo === 'pix')
                                Pix: {{ $transaction->destino }}
                            @endif
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Nenhuma movimentação encontrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
