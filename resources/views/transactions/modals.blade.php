@props(['categorias'])

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
                <input type="hidden" name="type" value="receita">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="valorReceita">Valor</label>
                        <input type="text" class="form-control" id="valorReceita" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="descricaoReceita">Descrição</label>
                        <input type="text" class="form-control" id="descricaoReceita" name="description" required>
                    </div>
                    <div class="form-group">
                        <label for="dataReceita">Data</label>
                        <input type="date" class="form-control" id="dataReceita" name="executed_at" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="categoriaReceita">Categoria</label>
                        <select class="form-control select2" id="categoriaReceita" name="categoria" required>
                            <option value="">Selecione uma categoria</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->nome }}">{{ $categoria->nome }}</option>
                            @endforeach
                        </select>
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
                <input type="hidden" name="type" value="despesa">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="valorDespesa">Valor</label>
                        <input type="text" class="form-control" id="valorDespesa" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="descricaoDespesa">Descrição</label>
                        <input type="text" class="form-control" id="descricaoDespesa" name="description" required>
                    </div>
                    <div class="form-group">
                        <label for="dataDespesa">Data</label>
                        <input type="date" class="form-control" id="dataDespesa" name="executed_at" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="categoriaDespesa">Categoria</label>
                        <select class="form-control select2" id="categoriaDespesa" name="categoria" required>
                            <option value="">Selecione uma categoria</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->nome }}">{{ $categoria->nome }}</option>
                            @endforeach
                        </select>
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
