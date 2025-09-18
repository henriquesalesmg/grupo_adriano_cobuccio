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
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Enviar link de recuperação</button>
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
