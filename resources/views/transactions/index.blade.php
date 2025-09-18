@extends('layouts.dashboard')

@section('title', 'Movimentações')

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
    <!-- Botão Nova Transação -->
    <div class="dropdown">
        <button class="btn btn-primary btn-icon-split dropdown-toggle" type="button" id="novaTransacaoDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Nova transação</span>
        </button>
        <div class="dropdown-menu" aria-labelledby="novaTransacaoDropdown">
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalReceita">Nova Receita</a>
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalDespesa">Nova Despesa</a>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Lista de Movimentações</h6>
            </div>
            <div class="card-body">
                <link href="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data</th>
                                <th>Valor</th>
                                <th>Descrição</th>
                                <th>Tipo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($transactions ?? [] as $transaction)
                            <tr>
                                <td>{{ $transaction->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                                <td>R$ {{ number_format($transaction->amount, 2, ',', '.') }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td>
                                    @if($transaction->type === 'receita')
                                        <span class="badge badge-success">Receita</span>
                                    @else
                                        <span class="badge badge-danger">Despesa</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton{{ $transaction->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Opções
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $transaction->id }}">
                                            <a class="dropdown-item" href="#">Editar</a>
                                            <a class="dropdown-item text-danger" href="#">Excluir</a>
                                        </div>
                                    </div>
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
@section('scripts')
    <script src="{{ asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/demo/datatables-demo.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
<!-- Modal Nova Receita -->
<div class="modal fade" id="modalReceita" tabindex="-1" role="dialog" aria-labelledby="modalReceitaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalReceitaLabel">Nova Receita</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formReceita" method="POST" action="{{ route('movimentacoes.store') }}">
                @csrf
                <input type="hidden" name="tipo" value="receita">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="valorReceita">Valor</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" id="valorReceita" name="valor" required>
                    </div>
                    <div class="form-group">
                        <label for="descricaoReceita">Descrição</label>
                        <input type="text" class="form-control" id="descricaoReceita" name="descricao" required>
                    </div>
                    <div class="form-group">
                        <label for="dataReceita">Data</label>
                        <input type="date" class="form-control" id="dataReceita" name="data" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar Receita</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Nova Despesa -->
<div class="modal fade" id="modalDespesa" tabindex="-1" role="dialog" aria-labelledby="modalDespesaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalDespesaLabel">Nova Despesa</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formDespesa" method="POST" action="{{ route('movimentacoes.store') }}">
                @csrf
                <input type="hidden" name="tipo" value="despesa">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="valorDespesa">Valor</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" id="valorDespesa" name="valor" required>
                    </div>
                    <div class="form-group">
                        <label for="descricaoDespesa">Descrição</label>
                        <input type="text" class="form-control" id="descricaoDespesa" name="descricao" required>
                    </div>
                    <div class="form-group">
                        <label for="dataDespesa">Data</label>
                        <input type="date" class="form-control" id="dataDespesa" name="data" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Salvar Despesa</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
            </div>
        </div>
    </div>
</div>
@endsection
