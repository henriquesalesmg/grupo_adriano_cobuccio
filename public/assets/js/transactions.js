$(document).ready(function() {
    // Máscara de valor no modal de edição
    $('#editarValor').mask('000.000.000,00', { reverse: true });

    // Bloqueio de múltiplos envios no modal de edição
    bloquearBotaoSubmit('#formEditarTransacao', 'button[type=submit]');
    // Bloqueio de duplo clique nos botões de confirmação dos modais
    function bloquearBotaoSubmit(formId, botaoSelector) {
        var submitted = false;
        $(formId).on('submit', function(e) {
            if (submitted) {
                e.preventDefault();
                return false;
            }
            submitted = true;
            var $btn = $(this).find(botaoSelector);
            $btn.prop('disabled', true);
            $btn.html('<span class="spinner-border spinner-border-sm mr-1"></span>Processando...');
        });
        // Ao fechar o modal, reseta o flag e o botão
        $(formId).closest('.modal').on('hidden.bs.modal', function() {
            submitted = false;
            var $btn = $(formId).find(botaoSelector);
            $btn.prop('disabled', false);
            $btn.html($btn.data('original-text') || $btn.text());
        });
        // Salva o texto original do botão
        $(formId).find(botaoSelector).each(function() {
            $(this).data('original-text', $(this).text());
        });
    }
    bloquearBotaoSubmit('#formExcluirTransacao', 'button[type=submit]');
    bloquearBotaoSubmit('#formReverterTransacao', 'button[type=submit]');
    bloquearBotaoSubmit('#formDeposito', 'button[type=submit]');
    bloquearBotaoSubmit('#formSaque', 'button[type=submit]');
    bloquearBotaoSubmit('#formTransferencia', 'button[type=submit]');
    // Só inicializa DataTables se houver pelo menos uma linha de dados real
    if ($('#dataTable tbody tr').not(':has(td[colspan])').length > 0) {
        $('#dataTable').DataTable({
            order: [[0, 'desc']], // Ordena pela primeira coluna (ID) decrescente
            language: {
                emptyTable: "Nenhuma movimentação encontrada."
            }
        });
    }
    // Máscara de moeda nos campos de valor dos modais
    $('#valorReceita, #valorDespesa, #valorDeposito').mask('000.000.000,00', { reverse: true });

    // Inicializa Select2 apenas ao abrir os modais, garantindo dropdownParent correto
    function initSelect2OnModal(modalId, selectId) {
        $(modalId).on('shown.bs.modal', function() {
            var $select = $(this).find(selectId);
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            $select.select2({
                tags: true,
                placeholder: 'Selecione ou digite para criar',
                allowClear: true,
                width: '100%',
                dropdownParent: $(this).find('.modal-content'),
                language: {
                    noResults: function() {
                        return 'Nenhuma categoria encontrada.';
                    },
                    inputTooShort: function() {
                        return 'Digite para buscar ou criar.';
                    }
                }
            });
        });
    }
    initSelect2OnModal('#modalReceita', '#categoriaReceita');
    initSelect2OnModal('#modalDespesa', '#categoriaDespesa');

    // Validação do filtro de datas
    document.getElementById('filtroForm').addEventListener('submit', function(e) {
        var dataInicio = document.getElementById('data_inicio').value;
        var dataFim = document.getElementById('data_fim').value;
        if (dataInicio && dataFim && dataInicio > dataFim) {
            e.preventDefault();
            alert('A data inicial não pode ser maior que a data final.');
        }
    });
});
