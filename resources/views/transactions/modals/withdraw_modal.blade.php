<!-- Modal Saque -->
<div class="modal fade" id="modalSaque" tabindex="-1" role="dialog" aria-labelledby="modalSaqueLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-warning text-dark">
				<h5 class="modal-title" id="modalSaqueLabel">Sacar</h5>
				<button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="formSaque" method="POST" action="{{ route('withdraw.store') }}">
				@csrf
				<div class="modal-body">
					<div class="form-group">
						<label for="valorSaque">Valor</label>
						<input type="text" class="form-control" id="valorSaque" name="amount" required>
						<small class="form-text text-muted">Saldo disponível: R$ {{ number_format($saldoAtual ?? 0, 2, ',', '.') }}</small>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-warning">Sacar</button>
				</div>
			</form>
		</div>
	</div>
</div>
@push('scripts')
<script>
	$(function(){
		$('#valorSaque').mask('#.##0,00', {reverse: true});
	});
</script>
@endpush
