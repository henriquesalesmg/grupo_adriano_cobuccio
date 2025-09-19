# Correção de Validação de Valores Monetários

## Problema Identificado
Quando o usuário digitava um valor como "2135", o sistema estava salvando como "2135,00" em vez de "21,35" como esperado.

## Causa
A lógica anterior de conversão era muito simples e apenas substituía separadores, sem considerar que valores digitados sem separador decimal deveriam ter os últimos 2 dígitos interpretados como centavos.

## Solução Implementada
Criada uma lógica mais inteligente no método `prepareForValidation()` dos Form Requests que:

### 1. Detecta o formato do valor de entrada:
- **Com separadores** (1.234,56 ou 123,45): Converte formato brasileiro para padrão
- **Sem separadores** (2135, 100, 50): Trata últimos 2 dígitos como centavos

### 2. Exemplos de conversão:
```
Entrada    -> Saída    -> Valor Real
2135       -> 21.35    -> R$ 21,35
100        -> 1.00     -> R$ 1,00
1250       -> 12.50    -> R$ 12,50
50         -> 0.50     -> R$ 0,50
5          -> 0.05     -> R$ 0,05
1.234,56   -> 1234.56  -> R$ 1.234,56
123,45     -> 123.45   -> R$ 123,45
```

### 3. Validação robusta:
- Remove caracteres não numéricos (exceto vírgula e ponto)
- Trata valores vazios adequadamente
- Mantém compatibilidade com formato brasileiro tradicional

## Arquivos Alterados
- `app/Http/Requests/TransactionRequest.php`
- `app/Http/Requests/DepositRequest.php`
- `app/Http/Requests/WithdrawRequest.php`

## Resultado
Agora quando o usuário digitar "2135", o sistema corretamente interpretará como R$ 21,35 e salvará o valor correto no banco de dados.