@extends('layouts.dashboard')

@section('content')

    <!-- Mensagem de boas-vindas -->
    <div class="alert alert-primary alert-dismissible fade show d-flex align-items-center justify-content-between mb-4" style="font-size:1.1rem;">
        <div>
            Olá, <strong>{{ $user->name }}</strong>! Você está no sistema financeiro.<br>
            Data de hoje: <strong>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</strong><br>
            Última ação realizada:
            @if($ultimaAtividade)
                <span class="text-dark">{{ $ultimaAtividade->action }} <small class="text-muted">({{ $ultimaAtividade->created_at->format('d/m/Y H:i') }})</small></span>
            @else
                <span class="text-muted">Nenhuma ação registrada ainda.</span>
            @endif
        </div>
        <div class="d-flex align-items-center">
            <i class="fas fa-user-circle fa-2x text-primary mr-3"></i>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="{{ route('report.dashboard.pdf') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-download fa-sm text-white-50"></i> Gerar Relatório</a>
    </div>

    @if(isset($reversalRequests) && $reversalRequests->count())
        <div class="alert alert-warning">
            <strong>Solicitações de reversão pendentes:</strong>
            <ul class="mb-0">
                @foreach($reversalRequests as $req)
                    <li>
                        Transferência de <b>{{ $req->requester->name }}</b> (R$ {{ number_format($req->transaction->amount, 2, ',', '.') }})
                        <form action="{{ route('reversal.approve', $req->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="btn btn-sm btn-success ml-2">Aprovar</button>
                        </form>
                        <form action="{{ route('reversal.reject', $req->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="btn btn-sm btn-danger ml-2">Rejeitar</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- Content Row -->
    <div class="row">
        <!-- Saldo Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">R$ {{ number_format($saldo, 2, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Transações Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Transações</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTransacoes }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Transferências Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Transferências</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTransferencias }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Reversões Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Reversões</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalReversoes }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-undo fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
