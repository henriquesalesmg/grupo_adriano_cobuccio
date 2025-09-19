# DTOs (Data Transfer Objects)

Este diretório contém os DTOs (Data Transfer Objects) do sistema financeiro. Os DTOs são responsáveis por encapsular e transferir dados entre as camadas da aplicação de forma organizada e padronizada.

## Estrutura dos DTOs

### 1. TransactionDTO
**Arquivo**: `TransactionDTO.php`
**Propósito**: Padroniza dados de transações (credit, debit, transfer)
**Uso**: Base para todas as operações transacionais

**Principais métodos**:
- `fromArray()`: Cria DTO a partir de array
- `toArray()`: Converte DTO para array
- `isCredit()`, `isDebit()`, `isTransfer()`: Verifica tipo de transação
- `getFormattedAmount()`: Formata valor monetário
- `getTypeLabel()`: Retorna label do tipo de transação

### 2. TransferDTO
**Arquivo**: `TransferDTO.php`
**Propósito**: Específico para operações de transferência entre contas
**Uso**: TransferController

**Principais métodos**:
- `fromRequest()`: Cria DTO a partir de request
- `withUserIdDestino()`: Atualiza com ID do usuário destino
- `getTransactionDataOrigem()`: Dados da transação de origem
- `getTransactionDataDestino()`: Dados da transação de destino
- `getActivityDescription()`: Descrição para log de atividade

### 3. ReversalDTO
**Arquivo**: `ReversalDTO.php`
**Propósito**: Gerencia dados de solicitações de reversão
**Uso**: ReversalRequestController

**Principais métodos**:
- `fromTransaction()`: Cria DTO a partir de transação
- `getReversalRequestData()`: Dados para tabela reversal_requests
- `getSymbolicTransactionData()`: Transação simbólica para extrato
- `getDebitTransactionData()`: Dados do débito na reversão
- `getCreditTransactionData()`: Dados do crédito na reversão

### 4. FinancialOperationDTO
**Arquivo**: `FinancialOperationDTO.php`
**Propósito**: Operações financeiras básicas (depósitos e saques)
**Uso**: DepositController, WithdrawController

**Principais métodos**:
- `createDeposit()`: Factory para depósito
- `createWithdraw()`: Factory para saque
- `fromRequest()`: Cria DTO a partir de request
- `getTransactionData()`: Dados para criação de transação
- `getActivityDescription()`: Descrição para activities

### 5. UserUpdateDTO
**Arquivo**: `UserUpdateDTO.php`
**Propósito**: Dados de atualização de perfil do usuário
**Uso**: UserSettingsController

**Principais métodos**:
- `fromRequest()`: Cria DTO a partir de request
- `getUpdateData()`: Dados para atualização
- `hasPasswordChange()`: Verifica se senha foi alterada
- `getCleanCpf()`: CPF limpo (apenas números)
- `validateRequiredFields()`: Validação de campos obrigatórios

### 6. DashboardDTO
**Arquivo**: `DashboardDTO.php`
**Propósito**: Dados consolidados do dashboard
**Uso**: DashboardController

**Principais métodos**:
- `fromUser()`: Cria DTO a partir do usuário
- `getFormattedSaldo()`: Saldo formatado
- `getSaldoStatus()`: Status do saldo (success/warning/danger)
- `getWelcomeMessage()`: Mensagem de boas-vindas
- `getAccountInfo()`: Informações da conta
- `getFinancialHealth()`: Saúde financeira

### 7. BalanceValidationDTO
**Arquivo**: `BalanceValidationDTO.php`
**Propósito**: Validação de saldo para operações financeiras
**Uso**: Controllers que precisam validar saldo

**Principais métodos**:
- `forUser()`: Cria DTO para usuário específico
- `hasSufficientBalance()`: Verifica se tem saldo suficiente
- `getBalanceDeficit()`: Calcula déficit de saldo
- `validateBalance()`: Validação completa com mensagem
- `getInsufficientBalanceMessage()`: Mensagem de saldo insuficiente

## Vantagens dos DTOs

1. **Encapsulamento**: Dados relacionados ficam agrupados
2. **Reutilização**: Lógica compartilhada entre controllers
3. **Padronização**: Formato consistente de dados
4. **Manutenibilidade**: Mudanças centralizadas
5. **Testabilidade**: Easier to unit test
6. **Type Safety**: PHP 8 typed properties
7. **Imutabilidade**: readonly properties previnem mutações acidentais

## Padrões de Uso

### Factory Methods
```php
// Criação a partir de request
$dto = TransferDTO::fromRequest($request->all(), $userId, $accountNumber);

// Criação específica
$dto = FinancialOperationDTO::createDeposit($userId, $amount);
```

### Conversão de Dados
```php
// DTO para array
$data = $dto->toArray();

// DTO para dados de transação
$transactionData = $dto->getTransactionData();
```

### Formatação
```php
// Valores formatados
$formattedAmount = $dto->getFormattedAmount();
$message = $dto->getActivityDescription();
```

### Validação
```php
// Validações específicas
if ($dto->hasSufficientBalance()) {
    // Prosseguir com operação
}

$validation = $dto->validateBalance();
if (!$validation['valid']) {
    return back()->with('error', $validation['message']);
}
```

## Controllers Refatorados

- ✅ `DashboardController`: Usa `DashboardDTO`
- ✅ `UserSettingsController`: Usa `UserUpdateDTO`
- ✅ `DepositController`: Usa `FinancialOperationDTO`
- ✅ `WithdrawController`: Usa `FinancialOperationDTO`
- 🔄 `TransferController`: Parcialmente refatorado
- 🔄 `ReversalRequestController`: Pode usar `ReversalDTO`
- 🔄 `TransactionController`: Pode usar `TransactionDTO`

## Próximos Passos

1. **Finalizar refatoração** dos controllers restantes
2. **Criar DTOs adicionais** conforme necessário
3. **Implementar testes unitários** para os DTOs
4. **Documentar exemplos de uso** mais detalhados
5. **Criar interfaces** para DTOs com comportamentos similares