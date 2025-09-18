<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carteira Financeira - Bem-vindo</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f8fafc 0%, #e2eafc 100%);
        }
        .welcome-card {
            border-radius: 1rem;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Carteira Financeira</a>
        </div>
    </nav>
    <main class="container d-flex flex-column justify-content-center align-items-center flex-grow-1 py-5">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card welcome-card p-4 my-5">
                <div class="card-body text-center">
                    <img src="{{ asset('logo.jpeg') }}" alt="Logo Grupo Adriano Cobuccio" class="mb-3 mx-auto d-block" style="max-width: 180px; width: 100%; height: auto;">
                    <h5 class="mb-4 fw-semibold">Grupo Adriano Cobuccio</h5>
                    <h1 class="card-title mb-3 fw-bold">Bem-vindo à sua Carteira Financeira</h1>
                    <p class="card-text mb-4">Gerencie seu dinheiro de forma simples, segura e prática.<br>Faça transferências, depósitos e acompanhe seu saldo em tempo real.</p>
                    <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4">Criar novo cadastro</a>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg px-4">Entrar</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="text-center text-muted py-3 small">
        &copy; {{ date('Y') }} Carteira Financeira. Todos os direitos reservados.
    </footer>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
