<!-- Modal Depósito -->
<div class="modal fade" id="modalDeposito" tabindex="-1" role="dialog" aria-labelledby="modalDepositoLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="modalDepositoLabel">Novo Depósito</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="formDeposito" method="POST" action="{{ route('deposit.store') }}">
				@csrf
				<div class="modal-body">
					<div class="form-group">
						<label for="valorDeposito">Valor</label>
						<input type="text" class="form-control" id="valorDeposito" name="amount" required>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary">Depositar</button>
				</div>
			</form>
		</div>
	</div>
</div>
