<!-- Modal Transferência -->
<div class="modal fade" id="modalTransferencia" tabindex="-1" role="dialog" aria-labelledby="modalTransferenciaLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-info text-white">
				<h5 class="modal-title" id="modalTransferenciaLabel">Realizar Transferência</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="formTransferencia" method="POST" action="{{ route('transfer.store') }}">
				@csrf
				<div class="modal-body">
					<input type="hidden" name="destino_tipo" value="conta">
					<div class="form-group">
						<label for="valorTransferencia">Valor</label>
						<input type="text" class="form-control" id="valorTransferencia" name="amount" required>
					</div>
					<div class="form-group">
						<label for="numeroConta">Selecione o destinatário</label>
						<select class="form-control" id="numeroConta" name="numero_conta" required>
							<option value="">Selecione uma conta</option>
							@php
								$contas = \App\Models\Account::with('user')->where('user_id', '!=', auth()->id())->get();
							@endphp
							@foreach($contas as $conta)
								<option value="{{ $conta->numero }}">
									{{ $conta->user->name }} - Agência {{ $conta->agencia }} Conta {{ $conta->numero }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label for="senhaTransferencia">Sua senha</label>
						<input type="password" class="form-control" id="senhaTransferencia" name="password" required autocomplete="current-password">
					</div>
					<div class="form-group">
						<label for="dataTransferencia">Data da Transferência</label>
						<input type="date" class="form-control" id="dataTransferencia" name="executed_at" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-info">Transferir</button>
				</div>
			</form>
		</div>
	</div>
</div>
@push('scripts')
<script>
	$(function(){
		$('#valorTransferencia').mask('#.##0,00', {reverse: true});
	});
</script>
@endpush
