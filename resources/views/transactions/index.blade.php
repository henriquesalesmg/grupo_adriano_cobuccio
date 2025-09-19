@extends('layouts.dashboard')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Movimentações</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
                <li class="breadcrumb-item active" aria-current="page">Movimentações</li>
            </ol>
        </div>
    </div>
    @if (isset($receivedTransfers) && $receivedTransfers->count())
        @foreach ($receivedTransfers as $transfer)
            @php
                $remetente = null;
                if ($transfer->destino) {
                    $contaRemetente = \App\Models\Account::where('numero', $transfer->destino)->first();
                    if ($contaRemetente) {
                        $remetente = $contaRemetente->user->name;
                    }
                }
            @endphp
            <div class="alert alert-info">
                <strong>Transferência recebida!</strong>
                Valor: <b>R$ {{ number_format($transfer->amount, 2, ',', '.') }}</b>
                @if ($remetente)
                    | Remetente: <b>{{ $remetente }}</b>
                @endif
            </div>
        @endforeach
    @endif
    @if (isset($reversalRequests) && $reversalRequests->count())
        <div class="alert alert-warning sticky-top" style="z-index:1050;">
            <strong>Solicitação de estorno recebida!</strong><br>
            <span>O usuário que lhe transferiu um valor solicitou a reversão do depósito. Autorize ou recuse abaixo:</span>
            <ul class="mb-0 mt-2">
                @foreach ($reversalRequests as $req)
                    <li class="mb-2">
                        <b>{{ $req->requester->name }}</b> solicita estorno de <b>R$
                            {{ number_format($req->transaction->amount, 2, ',', '.') }}</b>.<br>
                        <form action="{{ route('reversal.approve', $req->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="btn btn-sm btn-success ml-2">Autorizar estorno</button>
                        </form>
                        <form action="{{ route('reversal.reject', $req->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="btn btn-sm btn-danger ml-2">Recusar</button>
                        </form>
                    </li>
                @endforeach
            </ul>
            <span class="small text-muted">Este alerta ficará fixo até que você autorize ou recuse a solicitação.</span>
        </div>
    @endif
    <!-- Filtro de datas e categoria -->
    <form id="filtroForm" class="mb-4" method="GET" action="">
        <div class="form-row align-items-end">
            <div class="col-auto">
                <label for="data_inicio">Data início</label>
                <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="{{ request('data_inicio') }}">
            </div>
            <div class="col-auto">
                <label for="data_fim">Data fim</label>
                <input type="date" class="form-control" id="data_fim" name="data_fim" value="{{ request('data_fim') }}">
            </div>
            <div class="col-auto">
                <label for="categoria_filtro">Categoria</label>
                <input type="text" class="form-control" id="categoria_filtro" name="categoria_filtro" value="{{ request('categoria_filtro') }}">
            </div>
            <div class="col-auto">
                <label for="tipo_filtro">Tipo</label>
                <select class="form-control" id="tipo_filtro" name="tipo_filtro">
                    <option value="">Todos</option>
                    <option value="credit" {{ request('tipo_filtro') == 'credit' ? 'selected' : '' }}>Receita</option>
                    <option value="debit" {{ request('tipo_filtro') == 'debit' ? 'selected' : '' }}>Despesa</option>
                    <option value="transfer" {{ request('tipo_filtro') == 'transfer' ? 'selected' : '' }}>Transferência</option>
                </select>
            </div>
            <div class="col-auto d-flex align-items-end">
                <button type="submit" class="btn btn-primary mr-2">Filtrar</button>
                <a href="{{ route('transactions') }}" class="btn btn-outline-secondary">Limpar</a>
            </div>
        </div>
    </form>
    <!-- Cards Financeiros -->
    <div class="row mb-4">
        <div class="col-12 col-md-6 col-lg-3 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body p-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Atual</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        R$ {{ number_format($saldoAtual ?? 0, 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3 mb-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body p-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Valores a Receber</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        R$ {{ number_format($valoresReceber ?? 0, 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3 mb-3">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body p-2">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Contas a Pagar</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        R$ {{ number_format($contasPagar ?? 0, 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3 mb-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body p-2">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Saldo Futuro</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        R$ {{ number_format($saldoFuturo ?? 0, 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botões de Nova Receita e Nova Despesa (linha de cima) -->
    <div class="row mb-2 justify-content-center">
        <div class="col-12 text-center">
            <a class="btn btn-success mb-2 mx-1" href="#" data-toggle="modal" data-target="#modalReceita">
                <i class="fas fa-plus"></i> Nova Receita
            </a>
            <a class="btn btn-danger mb-2 mx-1" href="#" data-toggle="modal" data-target="#modalDespesa">
                <i class="fas fa-minus"></i> Nova Despesa
            </a>
        </div>
    </div>
    <!-- Botões de Depósito, Saque e Transferência (linha de baixo) -->
    <div class="row mb-4 justify-content-center">
        <div class="col-12 text-center">
            <a class="btn btn-primary mb-2 mx-1" href="#" data-toggle="modal" data-target="#modalDeposito">
                <i class="fas fa-university"></i> Novo Depósito
            </a>
            <a class="btn btn-warning mb-2 mx-1" href="#" data-toggle="modal" data-target="#modalSaque">
                <i class="fas fa-money-bill-wave"></i> Sacar
            </a>
            <a class="btn btn-info mb-2 mx-1" href="#" data-toggle="modal" data-target="#modalTransferencia">
                <i class="fas fa-exchange-alt"></i> Realizar Transferência
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary d-flex align-items-center">
                        Lista de Movimentações
                        @php
                            $filtros = [];
                            if (request('data_inicio') && request('data_fim')) {
                                $filtros[] =
                                    'entre as datas ' .
                                    \Carbon\Carbon::parse(request('data_inicio'))->format('d/m/Y') .
                                    ' e ' .
                                    \Carbon\Carbon::parse(request('data_fim'))->format('d/m/Y');
                            } elseif (request('data_inicio')) {
                                $filtros[] =
                                    'a partir de ' . \Carbon\Carbon::parse(request('data_inicio'))->format('d/m/Y');
                            } elseif (request('data_fim')) {
                                $filtros[] = 'até ' . \Carbon\Carbon::parse(request('data_fim'))->format('d/m/Y');
                            }
                            if (request('categoria_filtro')) {
                                $filtros[] = 'categoria: ' . request('categoria_filtro');
                            }
                            if (request('tipo_filtro')) {
                                $tipo = request('tipo_filtro') === 'credit' ? 'Receita' : (request('tipo_filtro') === 'debit' ? 'Despesa' : request('tipo_filtro'));
                                $filtros[] = 'tipo: ' . $tipo;
                            }
                        @endphp
                        @if (count($filtros))
                            <span class="ml-2 small text-muted">({{ implode(', ', $filtros) }})</span>
                        @endif
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive px-2">
                        <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Descrição</th>
                                    <th>Categoria</th>
                                    <th>Tipo</th>
                                    <th>Destino</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions ?? [] as $transaction)
                                    @if (is_object($transaction))
                                        <tr>
                                            <td>{{ $transaction->id }}</td>
                                            <td>{{ \Carbon\Carbon::parse($transaction->executed_at)->format('d/m/Y') }}</td>
                                            <td>R$ {{ number_format($transaction->amount, 2, ',', '.') }}</td>
                                            <td>
                                                @if (str_contains($transaction->description, 'Solicitação de reversão'))
                                                    <span class="text-info font-italic">{{ $transaction->description }}</span>
                                                @else
                                                    {{ $transaction->description }}
                                                @endif
                                                @if ($transaction->reverted)
                                                    <span class="text-danger font-italic"> (Operação revertida)</span>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->category ? $transaction->category->name : '-' }}</td>
                                            <td>
                                                @if ($transaction->type === 'credit')
                                                    <span class="badge badge-success">Receita</span>
                                                @elseif ($transaction->type === 'debit')
                                                    <span class="badge badge-danger">Despesa</span>
                                                @elseif ($transaction->type === 'transfer')
                                                    <span class="badge badge-warning">Transferência</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $transaction->type }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($transaction->type === 'transfer')
                                                    @if ($transaction->destino_tipo === 'conta')
                                                        Conta: {{ $transaction->destino }}
                                                    @elseif ($transaction->destino_tipo === 'pix')
                                                        Pix: {{ $transaction->destino }}
                                                    @else
                                                        -
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                        id="dropdownMenuButton{{ $transaction->id }}"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Opções
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $transaction->id }}">
                                                        @php
                                                            $hasPendingReversal = false;
                                                            if ($transaction->type === 'transfer' && isset($reversalRequests)) {
                                                                foreach ($reversalRequests as $req) {
                                                                    if ($req->transaction_id == $transaction->id && $req->status === 'pending') {
                                                                        $hasPendingReversal = true;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        @endphp
                                                        @if ($transaction->type === 'transfer' && !$transaction->reverted && !$hasPendingReversal)
                                                            <a class="dropdown-item text-warning btn-reverter-transacao" href="#" data-id="{{ $transaction->id }}">Reverter</a>
                                                        @endif
                                                        @if (($transaction->type === 'credit' || $transaction->type === 'debit') && $transaction->description !== 'Reversão de transferência (Operação revertida)' && $transaction->description !== 'Depósito')
                                                            <a class="dropdown-item text-danger btn-excluir-transacao" href="#" data-id="{{ $transaction->id }}">Excluir</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Nenhuma movimentação encontrada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>




    @include('transactions.modals', ['categorias' => $categorias])
    @include('transactions.modals.deposit_modal')
    @include('transactions.modals.withdraw_modal')
    @include('transactions.modals.transfer_modal')
    @include('transactions.modals.delete_modal')
    @include('transactions.modals.revert_modal')
    @include('transactions.modals.edit_modal')
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/transactions.js') }}"></script>
@endpush
