<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Comprovante de {{ $operacao }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .comprovante {
            border: 1px solid #ccc;
            padding: 24px;
            max-width: 400px;
            margin: 0 auto;
        }
        h2 { color: #333; }
        .info { margin-bottom: 10px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="comprovante">
        <h2>Comprovante de {{ $operacao }}</h2>
        <div class="info"><span class="label">Nome:</span> {{ $nome }}</div>
        <div class="info"><span class="label">Valor:</span> R$ {{ number_format($valor, 2, ',', '.') }}</div>
        <div class="info"><span class="label">Data:</span> {{ \Carbon\Carbon::parse($data)->format('d/m/Y H:i') }}</div>
        <div class="info"><span class="label">Operação:</span> {{ $operacao }}</div>
        @if($operacao == 'Transferência')
            <div class="info"><span class="label">Destino:</span>
                @if($destino_tipo == 'conta')
                    Conta: {{ $destino }}
                @elseif($destino_tipo == 'pix')
                    Pix: {{ $destino }}
                @endif
            </div>
        @endif
    </div>
</body>
</html>
