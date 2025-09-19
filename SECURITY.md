# Implementações de Segurança

Este documento descreve as funcionalidades de segurança implementadas no sistema financeiro.

## 1. Limite de Tentativas de Login

### 📋 Funcionalidades Implementadas

#### ✅ Middleware de Limite (`LoginLimitMiddleware`)
- **Localização**: `app/Http/Middleware/LoginLimitMiddleware.php`
- **Registro**: `bootstrap/app.php` (alias: `login.limit`)

#### ⚙️ Configurações de Segurança
```php
const MAX_ATTEMPTS_PER_EMAIL = 5;     // Máximo 5 tentativas por email
const MAX_ATTEMPTS_PER_IP = 10;       // Máximo 10 tentativas por IP
const LOCKOUT_TIME_MINUTES = 30;      // Bloqueio por 30 minutos
const ATTEMPTS_WINDOW_MINUTES = 15;   // Janela de 15 minutos para contar tentativas
```

#### 🔒 Sistema de Lockout
- **Bloqueio por Email**: Após 5 tentativas falhadas
- **Bloqueio por IP**: Após 10 tentativas falhadas (proteção contra ataques distribuídos)
- **Tempo de Bloqueio**: 30 minutos
- **Janela de Avaliação**: 15 minutos

#### 📊 Rastreamento de Tentativas
- **Tabela**: `login_attempts`
- **Campos Rastreados**:
  - Email do usuário
  - IP da tentativa
  - User Agent
  - Sucesso/falha
  - Timestamp da tentativa
  - Tempo de bloqueio

#### 🔍 Logs de Segurança
Todos os eventos de segurança são registrados:
- Tentativas de login bem-sucedidas
- Tentativas de login falhadas
- Contas bloqueadas
- Tentativas em contas já bloqueadas
- IPs com muitas tentativas

### 🚀 Como Usar

#### 1. Aplicar Middleware à Rota de Login
```php
// Em routes/web.php
Route::post('/login', [LoginController::class, 'login'])
    ->middleware('login.limit');
```

#### 2. Verificar Status de Bloqueio Programaticamente
```php
use App\Models\LoginAttempt;

// Verificar se conta está bloqueada
if (LoginAttempt::isAccountLocked($email)) {
    $remainingTime = LoginAttempt::getLockTimeRemaining($email);
    // Mostrar mensagem de bloqueio
}

// Obter número de tentativas falhadas
$failedAttempts = LoginAttempt::getFailedAttemptsCount($email);
```

#### 3. Limpeza de Dados Antigos
```bash
# Comando para limpar tentativas antigas (30+ dias)
php artisan tinker
>>> App\Models\LoginAttempt::cleanOldAttempts(30);
```

---

## 2. Criptografia de Dados Sensíveis

### 🔐 Funcionalidades Implementadas

#### ✅ Trait de Criptografia (`EncryptableFields`)
- **Localização**: `app/Traits/EncryptableFields.php`
- **Funcionalidade**: Criptografia automática de campos sensíveis

#### 🛡️ Campos Criptografados
No modelo `User`:
- **CPF**: Documento sensível
- **Security Answer**: Resposta de segurança

#### ⚙️ Características do Sistema
- **Criptografia Automática**: Campos são criptografados ao salvar
- **Descriptografia Automática**: Campos são descriptografados ao recuperar
- **Compatibilidade**: Funciona com dados existentes não criptografados
- **Detecção Inteligente**: Verifica se dados já estão criptografados

### 🔧 Implementação Técnica

#### 1. Configuração no Model
```php
use App\Traits\EncryptableFields;

class User extends Authenticatable
{
    use EncryptableFields;

    protected array $encryptable = [
        'cpf',
        'security_answer'
    ];
}
```

#### 2. Accessors e Mutators Automáticos
```php
// Accessor para CPF descriptografado
public function getCpfAttribute(): ?string
{
    return $this->getDecryptedAttribute('cpf');
}

// Mutator para CPF criptografado
public function setCpfAttribute(?string $value): void
{
    $this->setEncryptedAttribute('cpf', $value);
}
```

#### 3. Métodos de Busca Especiais
```php
// Busca por CPF (campo criptografado)
$user = User::findByCpf('12345678901');

// Busca genérica por campo criptografado
$users = User::searchByEncryptedField('cpf', $value);
```

### 📦 Comando de Migração de Dados

#### ✅ Comando `encrypt:existing-data`
```bash
# Testar sem fazer alterações
php artisan encrypt:existing-data --dry-run

# Criptografar todos os dados
php artisan encrypt:existing-data --force

# Criptografar campo específico
php artisan encrypt:existing-data --field=cpf --force

# Ver ajuda
php artisan encrypt:existing-data --help
```

#### 📊 Funcionalidades do Comando
- **Modo Dry Run**: Testa sem alterar dados
- **Progressão Visual**: Barra de progresso
- **Estatísticas**: Relatório de processamento
- **Detecção de Criptografia**: Não re-criptografa dados já protegidos
- **Tratamento de Erros**: Continua processamento mesmo com erros

### 🔍 Segurança Adicional

#### 1. Ocultação de Campos Sensíveis
```php
protected $hidden = [
    'password',
    'remember_token',
    'cpf',              // Oculto em JSON
    'security_answer',  // Oculto em JSON
];
```

#### 2. CPF Formatado para Exibição
```php
// Mostra apenas parte do CPF
$user->formatted_cpf; // "123.***.**-01"
```

#### 3. Verificação de Resposta de Segurança
```php
// Método seguro para verificar resposta
if ($user->verifySecurityAnswer($inputAnswer)) {
    // Resposta correta
}
```

---

## 3. Logs de Segurança

### 📝 Eventos Registrados

#### Login e Autenticação
```php
// Login bem-sucedido
Log::info('Login bem-sucedido', [
    'email' => $email,
    'ip' => $ip
]);

// Login falhado
Log::warning('Tentativa de login falhada', [
    'email' => $email,
    'ip' => $ip,
    'failed_attempts' => $attempts,
    'remaining_attempts' => $remaining
]);

// Conta bloqueada
Log::warning('Conta bloqueada por excesso de tentativas', [
    'email' => $email,
    'ip' => $ip,
    'failed_attempts' => $attempts,
    'lockout_minutes' => $minutes
]);
```

#### Tentativas Suspeitas
```php
// IP com muitas tentativas
Log::warning('IP com muitas tentativas de login', [
    'ip' => $ip,
    'user_agent' => $userAgent,
    'attempted_email' => $email
]);

// Tentativa em conta bloqueada
Log::warning('Tentativa de login em conta bloqueada', [
    'email' => $email,
    'ip' => $ip,
    'user_agent' => $userAgent,
    'lock_time_remaining' => $lockTime
]);
```

---

## 4. Configurações e Manutenção

### ⚙️ Configurações de Ambiente

#### Chave de Criptografia
```bash
# Gerar nova chave (cuidado: invalida dados criptografados existentes)
php artisan key:generate
```

#### Configurações de Database
```env
# Ajustar para campos TEXT maiores
DB_CONNECTION=mysql
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

### 🧹 Tarefas de Manutenção

#### 1. Limpeza de Tentativas Antigas
```bash
# Via comando artisan (criar se necessário)
php artisan auth:cleanup-attempts

# Via tinker
php artisan tinker
>>> App\Models\LoginAttempt::cleanOldAttempts(30);
```

#### 2. Monitoramento de Segurança
```bash
# Verificar tentativas recentes
php artisan tinker
>>> App\Models\LoginAttempt::where('attempted_at', '>=', now()->subHours(24))->count();

# Verificar contas bloqueadas
>>> App\Models\LoginAttempt::whereNotNull('locked_until')->where('locked_until', '>', now())->count();
```

#### 3. Criptografia de Novos Campos
```php
// 1. Adicionar campo ao array $encryptable
protected array $encryptable = [
    'cpf',
    'security_answer',
    'novo_campo_sensivel'  // Adicionar aqui
];

// 2. Criar accessors/mutators se necessário
// 3. Executar comando de migração
php artisan encrypt:existing-data --field=novo_campo_sensivel --force
```

---

## 5. Considerações de Performance

### ⚡ Otimizações Implementadas

#### 1. Índices de Database
- Email (para busca rápida)
- Tentativas por IP e data
- Tempo de bloqueio

#### 2. Cache de Verificações
```php
// Usar cache para verificações frequentes
Cache::remember("login_attempts_{$email}", 60, function() use ($email) {
    return LoginAttempt::getFailedAttemptsCount($email);
});
```

#### 3. Busca em Campos Criptografados
⚠️ **Atenção**: Busca em campos criptografados é lenta pois requer descriptografia de todos os registros.

**Alternativas**:
- Manter índices por hash para campos pesquisáveis
- Usar campos não-criptografados para busca (quando possível)
- Implementar cache para buscas frequentes

---

## 6. Próximos Passos

### 🔮 Melhorias Futuras

1. **2FA (Autenticação de Dois Fatores)**
   - TOTP via Google Authenticator
   - SMS de verificação
   - Códigos de backup

2. **Análise de Comportamento**
   - Detecção de login suspeito (localização, horário)
   - Fingerprinting de dispositivos
   - Alertas por email

3. **Auditoria Avançada**
   - Rastreamento de todas as ações críticas
   - IP, dispositivo, localização
   - Relatórios de segurança

4. **Backup Seguro**
   - Backup de dados criptografados
   - Rotação de chaves de criptografia
   - Recuperação de dados

## ✅ Status da Implementação

- ✅ **Limite de Tentativas de Login**: Implementado e funcional
- ✅ **Criptografia de Dados Sensíveis**: Implementado e testado
- ✅ **Logs de Segurança**: Implementado
- ✅ **Middleware de Segurança**: Implementado
- ✅ **Comando de Migração**: Implementado e testado
- ✅ **Documentação**: Completa

## 🛡️ Resumo de Segurança

O sistema agora conta com proteções robustas contra:
- **Ataques de Força Bruta**: Limite de tentativas
- **Vazamento de Dados**: Criptografia de campos sensíveis
- **Ataques Distribuídos**: Limite por IP
- **Monitoramento**: Logs detalhados de segurança
- **Compatibilidade**: Funciona com dados existentes
