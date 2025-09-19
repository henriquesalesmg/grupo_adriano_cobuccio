<?php

// Teste da nova lógica de conversão (formato simplificado)

function convertAmountSimplified($input) {
    $amount = trim($input);
    
    // Remove espaços e caracteres não numéricos
    $amount = preg_replace('/[^\d]/', '', $amount);
    
    if (empty($amount)) {
        return '';
    }
    
    // Todos os campos agora usam máscara JavaScript que envia valores como centavos
    // Exemplo: usuário digita "12,34" -> JavaScript envia "1234" -> PHP converte para "12.34"
    $amount = number_format($amount / 100, 2, '.', '');
    
    return $amount;
}

// Casos de teste com a nova lógica
$tests = [
    '1234' => '12.34',      // Problema original: 12,34 vira 1234 no JS
    '100' => '1.00',        // 1 real
    '1250' => '12.50',      // 12 reais e 50 centavos
    '50' => '0.50',         // 50 centavos
    '5' => '0.05',          // 5 centavos
    '123456' => '1234.56',  // Valor maior
    '999999' => '9999.99',  // Valor máximo comum
    '0' => '0.00',          // Zero
];

echo "Teste da nova lógica simplificada:\n\n";

foreach ($tests as $input => $expected) {
    $result = convertAmountSimplified($input);
    $status = ($result === $expected) ? '✓' : '✗';
    echo sprintf("%s Input: %-8s | Expected: %-8s | Result: %-8s\n", 
        $status, $input, $expected, $result);
}