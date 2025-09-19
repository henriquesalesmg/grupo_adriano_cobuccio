@extends('layouts.dashboard')

@section('content')
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h3>Minhas Solicitações de Reversão</h3>
                <form method="GET" class="form-inline mb-3">
                    <div class="form-group mr-2">
                        <label for="data_inicio" class="mr-2">Data início:</label>
                        <input type="date" name="data_inicio" id="data_inicio" class="form-control"
                            value="{{ request('data_inicio') }}">
                    </div>
                    <div class="form-group mr-2">
                        <label for="data_fim" class="mr-2">Data fim:</label>
                        <input type="date" name="data_fim" id="data_fim" class="form-control"
                            value="{{ request('data_fim') }}">
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Filtrar</button>
                    <a href="{{ route('reversals.index') }}" class="btn btn-outline-secondary">Limpar</a>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Lista de Reversões Solicitadas</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive px-2">
                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Valor</th>
                                        <th>Destinatário</th>
                                        <th>Agência</th>
                                        <th>Conta</th>
                                        <th>Data/Hora Solicitação</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reversals as $reversal)
                                        <tr>
                                            <td>R$ {{ number_format($reversal->transaction->amount, 2, ',', '.') }}</td>
                                            <td>{{ $reversal->receiver->name ?? '-' }}</td>
                                            <td>{{ $reversal->receiver->account->agencia ?? '-' }}</td>
                                            <td>{{ $reversal->receiver->account->numero ?? '-' }}</td>
                                            <td>{{ $reversal->created_at ? $reversal->created_at->format('d/m/Y H:i') : '-' }}
                                            </td>
                                            <td>
                                                @if ($reversal->status === 'pending')
                                                    <span class="badge badge-warning">Pendente</span>
                                                @elseif($reversal->status === 'approved')
                                                    <span class="badge badge-success">Aprovada</span>
                                                @elseif($reversal->status === 'rejected')
                                                    <span class="badge badge-danger">Recusada</span>
                                                @else
                                                    <span
                                                        class="badge badge-secondary">{{ ucfirst($reversal->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Nenhuma solicitação de reversão
                                                encontrada.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap4.min.css">
@endpush

@push('scripts')
    <x-scripts_table />
    <script>
        $(document).ready(function() {
            if ($('#dataTable tbody tr').not(':has(td[colspan])').length > 0) {
                $('#dataTable').DataTable({
                    order: [
                        [4, 'desc']
                    ],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
                        emptyTable: 'Nenhuma solicitação de reversão encontrada.'
                    },
                    dom: '<"row mb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 text-right"B>>' +
                        '<"row"<"col-sm-12"tr>>' +
                        '<"row mt-2"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    pagingType: "simple_numbers",
                    buttons: [{
                            extend: 'excelHtml5',
                            className: 'btn btn-sm btn-success',
                            text: '<i class="fas fa-file-excel"></i> Excel'
                        },
                        {
                            extend: 'csvHtml5',
                            className: 'btn btn-sm btn-info',
                            text: '<i class="fas fa-file-csv"></i> CSV'
                        },
                        {
                            extend: 'pdfHtml5',
                            className: 'btn btn-sm btn-danger',
                            text: '<i class="fas fa-file-pdf"></i> PDF'
                        },
                        {
                            extend: 'print',
                            className: 'btn btn-sm btn-secondary',
                            text: '<i class="fas fa-print"></i> Imprimir'
                        }
                    ]
                });
            }
        });
    </script>
@endpush
