# Plataforma de Gestão de Projetos

Sistema web para criar projetos, convidar membros, organizar tarefas em um quadro Kanban, comentar e acompanhar o andamento em um painel. Trabalho Avaliativo 3 de Programação de Sistemas Web (Afya, 6º período, 2026/2), Equipe 1.

## Equipe

João Pedro Meira, David Emanuel, Marcelo Talles, Júlia Mota, Pedro Henrique Cardoso Maia e Djalma Cardoso.

## Funcionalidades

- Cadastro, login e logout de usuários
- Criação, edição e arquivamento de projetos
- Convite de membros com papéis (proprietário, membro)
- Tarefas com título, descrição, responsável, prioridade, prazo e status
- Quadro Kanban com arrastar e soltar entre colunas
- Comentários nas tarefas
- Histórico de atividades do projeto
- Painel com indicadores: tarefas por status, atrasadas e por responsável

## Tecnologias

PHP 8.3+, Laravel 13, MySQL 8, Blade, Tailwind CSS, Alpine.js, Vite.

## Requisitos

PHP 8.3+, Composer 2, Node 20+, MySQL 8 em execução local.

## Instalação

```bash
git clone <url-do-repositorio> gestao-projetos
cd gestao-projetos
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Crie o banco (`CREATE DATABASE gestao_projetos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`), ajuste `DB_*` no `.env` e execute:

```bash
php artisan migrate:fresh --seed
```

## Execução

Em dois terminais:

```bash
php artisan serve     # http://localhost:8000
npm run dev
```

## Acesso de demonstração

Criado pelo seeder, somente para ambiente local:

| Usuário | E-mail | Senha |
|---|---|---|
| Administrador | admin@demo.test | password |
| Membro | membro@demo.test | password |

## Testes

```bash
php artisan test
```

## Documentação

Ver a pasta `docs/`. Comece por `docs/ARQUITETURA.md`.
