<!-- Modal de Confirmação de Reversão -->
<div class="modal fade" id="modalReverterTransacao" tabindex="-1" role="dialog" aria-labelledby="modalReverterTransacaoLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-warning text-dark">
				<h5 class="modal-title" id="modalReverterTransacaoLabel">Solicitar Reversão de Transferência</h5>
				<button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="formReverterTransacao" method="POST">
				@csrf
				<div class="modal-body">
					<p>Ao solicitar a reversão, o usuário que recebeu esta transferência será notificado e deverá autorizar o estorno do valor.</p>
					<ul>
						<li>O destinatário verá um alerta fixo na tela informando sobre a solicitação de estorno.</li>
						<li>Ele poderá autorizar ou recusar a reversão.</li>
						<li>Se autorizado, o valor será debitado da conta do destinatário (caso haja saldo suficiente) e creditado de volta na sua conta.</li>
						<li>Você verá o estorno como uma transferência recebida com a descrição de estorno do depósito realizado indevidamente.</li>
					</ul>
					<p>Deseja realmente solicitar a reversão desta transferência?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-warning">Solicitar Reversão</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		$(document).on('click', '.btn-reverter-transacao', function(e) {
			e.preventDefault();
			const transacaoId = $(this).data('id');
			// Corrigir para rota reversal.request
			const action = "{{ url('/reversal-request') }}/" + transacaoId;
			$('#formReverterTransacao').attr('action', action);
			$('#modalReverterTransacao').modal('show');
		});
	});
</script>
