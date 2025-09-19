<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalExcluirTransacao" tabindex="-1" role="dialog" aria-labelledby="modalExcluirTransacaoLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-danger text-white">
				<h5 class="modal-title" id="modalExcluirTransacaoLabel">Confirmar Exclusão</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="formExcluirTransacao" method="POST">
				@csrf
				@method('DELETE')
				<div class="modal-body">
					<p>Tem certeza que deseja excluir esta transação? Esta ação não poderá ser desfeita.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-danger">Excluir</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		let transacaoId = null;
		$(document).on('click', '.btn-excluir-transacao', function(e) {
			e.preventDefault();
			transacaoId = $(this).data('id');
			const action = "{{ url('/transaction') }}/" + transacaoId;
			$('#formExcluirTransacao').attr('action', action);
			$('#modalExcluirTransacao').modal('show');
		});
	});
</script>
