@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h2 class="mb-4">Relatório de Movimentações</h2>
    <form method="GET" class="mb-4">
        <div class="form-row align-items-end">
            <div class="form-group col-md-3">
                <label for="data_inicio">Data inicial</label>
                <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="{{ request('data_inicio') }}">
            </div>
            <div class="form-group col-md-3">
                <label for="data_fim">Data final</label>
                <input type="date" class="form-control" id="data_fim" name="data_fim" value="{{ request('data_fim') }}">
            </div>
            <div class="form-group col-md-3">
                <label for="tipo">Tipo</label>
                <select class="form-control" id="tipo" name="tipo">
                    <option value="">Todos</option>
                    <option value="credit" {{ request('tipo') == 'credit' ? 'selected' : '' }}>Receita</option>
                    <option value="debit" {{ request('tipo') == 'debit' ? 'selected' : '' }}>Despesa</option>
                    <option value="transfer" {{ request('tipo') == 'transfer' ? 'selected' : '' }}>Transferência</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('report.pdf', request()->all()) }}" class="btn btn-outline-danger ml-2" target="_blank">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
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
    </div>
</div>
@endsection
