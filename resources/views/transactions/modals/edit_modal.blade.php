<!-- Modal Editar Receita/Despesa -->
<div class="modal fade" id="modalEditarTransacao" tabindex="-1" role="dialog" aria-labelledby="modalEditarTransacaoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEditarTransacaoLabel">Editar Receita/Despesa</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditarTransacao" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editarDescricao">Descrição</label>
                        <input type="text" class="form-control" id="editarDescricao" name="description" required>
                    </div>
                    <div class="form-group">
                        <label for="editarValor">Valor</label>
                        <input type="text" class="form-control" id="editarValor" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="editarData">Data</label>
                        <input type="date" class="form-control" id="editarData" name="executed_at" required>
                    </div>
                    <div class="form-group">
                        <label for="editarCategoria">Categoria</label>
                        <select class="form-control" id="editarCategoria" name="category_transaction_id">
                            <option value="">Selecione</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $(document).on('click', '.dropdown-menu .dropdown-item:not(.btn-excluir-transacao):not(.btn-reverter-transacao)', function(e) {
            if ($(this).text().trim() === 'Editar') {
                e.preventDefault();
                const row = $(this).closest('tr');
                const id = row.find('td:first').text().trim();
                const descricao = row.find('td:nth-child(4)').text().trim();
                const valor = row.find('td:nth-child(3)').text().replace('R$', '').trim();
                const data = row.find('td:nth-child(2)').text().split('/').reverse().join('-');
                const categoria = row.find('td:nth-child(5)').text().trim();
                // Preenche os campos do modal
                $('#editarDescricao').val(descricao);
                $('#editarValor').val(valor);
                $('#editarData').val(data);
                $('#editarCategoria option').filter(function() { return $(this).text() === categoria; }).prop('selected', true);
                // Define a action do form
                $('#formEditarTransacao').attr('action', `/transaction/${id}`);
                $('#modalEditarTransacao').modal('show');
            }
        });
    });
</script>
