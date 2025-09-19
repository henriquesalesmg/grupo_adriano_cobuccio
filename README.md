<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Sistema Financeiro - Adriano Cobuccio

> Sistema de gestão financeira pessoal, desenvolvido em Laravel 12, com arquitetura orientada a serviços (Service Layer), Repository, SOLID, autenticação, relatórios, extratos, transferências, reversões, comprovantes em PDF e histórico de atividades.

---

## Sumário
- [Descrição do Projeto](#descrição-do-projeto)
- [Pré-requisitos](#pré-requisitos)
- [Tecnologias Utilizadas](#tecnologias-utilizadas)
- [Funcionalidades](#funcionalidades)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Instruções de Instalação e Execução](#instruções-de-instalação-e-execução)
- [Executando com Docker](#executando-com-docker)
- [Seeders e Banco de Dados](#seeders-e-banco-de-dados)
- [APIs e Rotas](#apis-e-rotas)
- [Decisões de Arquitetura](#decisões-de-arquitetura)
- [Medidas de Segurança](#medidas-de-segurança)
- [Diagrama UML do Banco de Dados](#diagrama-uml-do-banco-de-dados)
- [Relatório de Testes Automatizados](#relatório-de-testes-automatizados)

## Descrição do Projeto
Sistema de controle financeiro pessoal, com cadastro de usuários, contas, categorias, movimentações (receitas, despesas, transferências), extratos, relatórios em PDF, reversão de transações, histórico de atividades e comprovantes. O sistema segue padrões SOLID, Service Layer, Repository e utiliza validações robustas em todas as operações.

## Pré-requisitos
- PHP >= 8.2
- Composer
- MySQL ou PostgreSQL
- Node.js (para assets)
- Docker (opcional)

## Tecnologias Utilizadas
- **Backend:** Laravel 12, Eloquent ORM
- **Frontend:** Blade, Bootstrap 5
- **PDF:** barryvdh/laravel-dompdf
- **Testes:** PHPUnit
- **Arquitetura:** Service Layer, Repository, DTOs, FormRequest, Traits

## Funcionalidades
- Cadastro e autenticação de usuários
- Cadastro de contas bancárias
- Movimentações: receitas, despesas, transferências
- Relatórios e extratos filtráveis (PDF)
- Histórico de transferências e atividades
- Reversão de transações
- Comprovantes em PDF
- Validações robustas (FormRequest)
- Seeders para dados de exemplo

## Estrutura do Projeto
```
/
├── app/
│   ├── Http/Controllers
│   ├── Services/
│   ├── Models/
│   ├── Traits/
│   └── DTOs/
├── resources/views/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── public/
├── tests/
├── .env
└── README.md
```

## Instruções de Instalação e Execução
1. Clone o repositório:
   ```bash
   git clone https://github.com/henriquesalesmg/grupo_adriano_cobuccio.git
   cd adriano_cobuccio
   ```
2. Instale as dependências:
   ```bash
   composer install
   npm install && npm run dev
   ```
3. Configure o `.env` e gere a key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Execute as migrações e seeders:
   ```bash
   php artisan migrate --seed
   ```
5. Inicie o servidor:
   ```bash
   php artisan serve
   ```
6. Acesse em [http://localhost:8000](http://localhost:8000)

## Executando com Docker (Automático)

Agora basta rodar **um único comando** e todo o ambiente será preparado automaticamente (dependências, key, migrations, seeders, build de assets):

```sh
docker compose up -d --build
```

Acesse o sistema em [http://localhost:8080](http://localhost:8080)

> **Nota:** O Dockerfile e o entrypoint automatizam todo o setup do ambiente. Após rodar `docker compose up -d --build`, não é necessário executar comandos manuais para instalar dependências, rodar migrations, seeders ou gerar a key do Laravel. Tudo é feito automaticamente!

## Seeders e Banco de Dados
- O seeder `DemoUsersAndTransactionsSeeder` cria 4 usuários, cada um com conta e 5 transações (crédito, débito, transferência), facilitando testes e visualização dos relatórios e extratos.
- Estrutura das tabelas e relacionamentos pode ser visualizada no diagrama UML abaixo.

## APIs e Rotas
- Rotas web protegidas por autenticação.
- Endpoints principais:
    - `/login`, `/register`, `/transactions`, `/deposit`, `/withdraw`, `/transfer`, `/relatorio`, `/historical`, `/reversals`, `/activities`, `/receipt/{type}/{id}`
- Todas as rotas validam dados via FormRequest.

## Decisões de Arquitetura
- **Service Layer:** Toda regra de negócio centralizada em Services, controllers apenas orquestram.
- **Repository:** (em evolução) Criação de TransactionRepository para encapsular persistência.
- **DTOs:** Transferência de dados entre camadas.
- **FormRequest:** Validação e normalização de dados.
- **Traits:** Reutilização de lógica (ex: EncryptableFields).
- **SOLID:** Código desacoplado, testável e de fácil manutenção.

## Medidas de Segurança
- Autenticação obrigatória
- Senhas criptografadas
- Validação de entrada em todas as rotas
- Proteção CSRF
- Rate limiting no login

## Diagrama UML do Banco de Dados

O diagrama UML das tabelas principais está disponível em formato PDF na pasta `public` do projeto.

- [Clique aqui para visualizar o diagrama UML (PDF)](public/diagrama_uml.pdf)

> O arquivo `diagrama_uml.pdf` apresenta a estrutura das tabelas, relacionamentos e principais campos do banco de dados do sistema.

## Relatório de Testes Automatizados

- Todos os testes automatizados do sistema passaram com sucesso na última execução (19/09/2025).
- Para detalhes completos, consulte o relatório em [`docs/test-report.md`](docs/test-report.md).

### Como executar os testes

```sh
docker compose exec app vendor/bin/phpunit --testdox
```

> **Status:** Todos os testes passaram ✔️

---

> Sistema desenvolvido para fins de avaliação técnica, destacando experiência com Laravel, arquitetura SOLID, Service Layer, Repository, Docker e boas práticas de segurança e validação.
