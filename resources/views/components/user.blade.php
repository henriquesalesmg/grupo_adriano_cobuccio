<!-- Modal Configurações de Conta -->
<div class="modal fade" id="settingsModal" tabindex="-1" role="dialog"
    aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="settingsModalLabel">Configurações da Conta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="settingsForm" method="POST" action="{{ route('user.settings.update') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="settingsName">Nome</label>
                        <input type="text" class="form-control" id="settingsName" name="name" value="{{ Auth::user()->name }}" required>
                    </div>
                    <div class="form-group">
                        <label for="settingsEmail">E-mail <span class="text-muted">(alteração apenas pelo administrador)</span></label>
                        <input type="email" class="form-control" id="settingsEmail" value="{{ Auth::user()->email }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="settingsCpf">CPF <span class="text-muted">(alteração apenas pelo administrador)</span></label>
                        <input type="text" class="form-control" id="settingsCpf" value="{{ Auth::user()->cpf }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="settingsPassword">Nova Senha</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="settingsPassword" name="password" placeholder="Deixe em branco para não alterar">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <small class="form-text text-muted mt-2">
                            A senha deve conter pelo menos:
                            <ul class="mb-0">
                                <li>6 caracteres</li>
                                <li>1 letra maiúscula</li>
                                <li>1 letra minúscula</li>
                                <li>1 número</li>
                                <li>1 caractere especial</li>
                            </ul>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('settingsPassword');
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
    });
</script>
<div>
    <!-- Simplicity is the ultimate sophistication. - Leonardo da Vinci -->
</div>
