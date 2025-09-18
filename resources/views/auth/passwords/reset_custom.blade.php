@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="mb-4 text-center">Definir nova senha</h2>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('password.update.custom') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ old('email', $email ?? '') }}">
                        <div class="mb-3">
                            <label for="password" class="form-label">Nova senha</label>
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
                            <label for="password_confirmation" class="form-label">Confirme a nova senha</label>
                            <div class="input-group">
                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1" data-target="password_confirmation">
                                    <span class="fa fa-eye"></span>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Redefinir senha</button>
                    </form>
                    <div class="mt-3 text-center">
                        <a href="{{ route('login') }}">Voltar ao login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const passwordInput = document.getElementById('password');
const passwordHelp = document.getElementById('passwordHelp');
const form = document.querySelector('form');
function validatePassword(password) {
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
