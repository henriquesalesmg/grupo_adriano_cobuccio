# Relatório de Testes de Sucesso

---

## Log de Execução de Testes

### Teste em 17/09/2025 18:08

1. **Acesso à página inicial**
   - Usuário pode acessar a página de boas-vindas (`/`)
   - Resultado: Sucesso

2. **Cadastro de usuário**
   - Usuário pode se registrar com nome, e-mail e senha válidos (`/register`)
   - Resultado: Sucesso

3. **Login de usuário**
   - Usuário pode fazer login com e-mail e senha válidos (`/login`)
   - Resultado: Sucesso

4. **Acesso ao dashboard autenticado**
   - Usuário autenticado pode acessar o dashboard (`/dashboard`)
   - Resultado: Sucesso

5. **Logout**
   - Usuário autenticado pode fazer logout e é redirecionado corretamente
   - Resultado: Sucesso

6. **Proteção de rota do dashboard**
   - Usuário não autenticado tentando acessar /dashboard é redirecionado para login
   - Resultado: Sucesso

7. **Acesso às views de login e registro**
   - As páginas de login e registro carregam corretamente
   - Resultado: Sucesso

---

### Teste em 17/09/2025 19:33

1. **Acesso à página inicial**
   - Usuário pode acessar a página de boas-vindas (`/`)
   - Resultado: Sucesso

2. **Cadastro de usuário**
   - Usuário pode se registrar com nome, e-mail, CPF e senha forte válidos (`/register`)
   - Resultado: Sucesso

3. **Login de usuário**
   - Usuário pode fazer login com e-mail, CPF e senha forte válidos (`/login`)
   - Resultado: Sucesso

4. **Acesso ao dashboard autenticado**
   - Usuário autenticado pode acessar o dashboard (`/dashboard`)
   - Resultado: Sucesso

5. **Logout**
   - Usuário autenticado pode fazer logout e é redirecionado corretamente
   - Resultado: Sucesso

6. **Proteção de rota do dashboard**
   - Usuário não autenticado tentando acessar /dashboard é redirecionado para login
   - Resultado: Sucesso

7. **Acesso às views de login e registro**
   - As páginas de login e registro carregam corretamente
   - Resultado: Sucesso

---

Todos os testes de sucesso passaram conforme esperado, incluindo CPF obrigatório e senha forte.
# Relatório de Testes

Data: 19/09/2025

## Resumo
Todos os testes automatizados do sistema passaram com sucesso, garantindo a integridade das principais funcionalidades:

- Cadastro e autenticação de usuários
- Fluxo de transações financeiras (crédito, débito, edição, restrição de saldo)
- Cobertura de rotas e redirecionamentos

## Execução
Os testes foram executados com o comando:

```
docker compose exec app vendor/bin/phpunit --testdox
```

## Resultado
```
Todos os testes passaram (100%).
```

## Documentação
Consulte o relatório de testes completo em [docs/test-report.md](docs/test-report.md).

---

# Documentação do Projeto

## Testes Automatizados

- Todos os testes automatizados estão localizados na pasta `tests/`.
- Para rodar os testes, utilize:

```
docker compose exec app vendor/bin/phpunit --testdox
```

- O relatório detalhado dos testes pode ser acessado em: [docs/test-report.md](docs/test-report.md)

> **Status:** Todos os testes passaram na última execução (19/09/2025).
