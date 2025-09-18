@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="mb-4 text-center">Registrar</h2>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome</label>
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="cpf" class="form-label">CPF</label>
                            <input id="cpf" type="text" maxlength="14" class="form-control" name="cpf" value="{{ old('cpf') }}" required placeholder="000.000.000-00">
                        </div>
                        <div class="mb-3">
                            <label for="birthdate" class="form-label">Data de Nascimento</label>
                            <input id="birthdate" type="date" class="form-control" name="birthdate" value="{{ old('birthdate') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="security_answer" class="form-label">Qual o seu time de futebol favorito?</label>
                            <input id="security_answer" type="text" class="form-control" name="security_answer" value="{{ old('security_answer') }}" required placeholder="Ex: Cruzeiro">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <div class="input-group">
                                <input id="password" type="password" class="form-control" name="password" required aria-describedby="passwordHelp">
                                <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1" data-target="password">
                                    <span class="fa fa-eye"></span>
                                </button>
                            </div>
                            <div id="passwordHelp" class="form-text mt-2 text-danger d-none">
                                A senha deve ter pelo menos 6 caracteres, incluindo uma letra maiúscula, uma minúscula, um número e um caractere especial.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirme a senha</label>
                            <div class="input-group">
                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1" data-target="password_confirmation">
                                    <span class="fa fa-eye"></span>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Registrar</button>
                    </form>
                    <div class="mt-3 text-center">
                        <a href="{{ route('login') }}">Já tem conta? Entrar</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5 col-lg-4 mt-4 mt-md-0">
            <div class="card border-info shadow-sm small">
                <div class="card-body p-3">
                    <h6 class="card-title text-info mb-2" style="font-size: 1rem;">CPF's para criação de teste:</h6>
                    <ul class="mb-0 ps-3" style="font-size: 0.95rem;">
                        <li>245.167.530-65</li>
                        <li>795.769.820-49</li>
                        <li>157.226.290-73</li>
                        <li>259.406.770-96</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
const passwordInput = document.getElementById('password');
const passwordHelp = document.getElementById('passwordHelp');
const form = document.querySelector('form');

function validatePassword(password) {
    // Mínimo 6 caracteres, pelo menos 1 maiúscula, 1 minúscula, 1 número e 1 caractere especial
    return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).{6,}$/.test(password);
}

passwordInput.addEventListener('input', function() {
    if (!validatePassword(passwordInput.value)) {
        passwordHelp.classList.remove('d-none');
        passwordInput.classList.add('is-invalid');
    } else {
        passwordHelp.classList.add('d-none');
        passwordInput.classList.remove('is-invalid');
    }
});

form.addEventListener('submit', function(e) {
    if (!validatePassword(passwordInput.value)) {
        passwordHelp.classList.remove('d-none');
        passwordInput.classList.add('is-invalid');
        passwordInput.focus();
        e.preventDefault();
    }
});

document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', function() {
        const target = document.getElementById(this.dataset.target);
        if (target.type === 'password') {
            target.type = 'text';
            this.querySelector('span').classList.remove('fa-eye');
            this.querySelector('span').classList.add('fa-eye-slash');
        } else {
            target.type = 'password';
            this.querySelector('span').classList.remove('fa-eye-slash');
            this.querySelector('span').classList.add('fa-eye');
        }
    });
});
</script>
@endpush
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
