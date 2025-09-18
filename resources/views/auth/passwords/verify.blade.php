@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="mb-4 text-center">Recuperar senha</h2>
                    @if (session('status'))
                        <div class="alert alert-success text-center">{{ session('status') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('password.verify') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
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
                        <button type="submit" class="btn btn-primary w-100">Validar dados</button>
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
