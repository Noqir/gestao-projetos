# AGENTS.md

Instruções para qualquer agente de código (Claude Code, Codex, Gemini CLI etc.) que trabalhe neste repositório. Este arquivo é a fonte única de verdade; o `CLAUDE.md` o importa.

## Projeto

Plataforma de Gestão de Projetos (Equipe 1), desenvolvida para o Trabalho Avaliativo 3 da disciplina Programação de Sistemas Web (6º período, 2026/2, Afya). O sistema permite criar projetos, convidar membros, organizar tarefas em um quadro Kanban, comentar e acompanhar o progresso em um painel.

Apresentação em 13/11/2026 (10 minutos, todos os membros falam). Após 03/12/2026 a nota máxima cai para 5 pontos; após 04/12/2026, para 2,5.

## Prioridades (em ordem)

1. **O sistema precisa funcionar** de ponta a ponta em demonstração ao vivo. Funcionamento é obrigatório.
2. **A arquitetura precisa estar documentada e ser explicável**, em especial a comunicação entre banco de dados, camada de persistência e back-end. É o ponto que o professor mais valoriza. Qualquer mudança estrutural deve atualizar `docs/ARQUITETURA.md` e `docs/BANCO_DE_DADOS.md` no mesmo commit.
3. **A estética da interface e da apresentação** é muito avaliada: layout limpo, consistente, responsivo, com estados vazios e feedback visual.
4. Funcionalidade além do escopo mínimo é secundária.

## Stack

- PHP 8.3+ e Laravel 13 (framework permitido pelo enunciado; ver `docs/adr/0002-laravel-13.md`)
- MySQL 8 (banco relacional obrigatório); SQLite em memória apenas para testes
- Blade + Tailwind CSS + Alpine.js, empacotados com Vite
- Autenticação: Laravel Breeze (Blade)
- Testes: Pest ou PHPUnit

Justificativas em `docs/adr/0001-stack.md`.

## Comandos

```bash
composer install && npm install        # dependências
cp .env.example .env && php artisan key:generate
php artisan migrate:fresh --seed       # recria o banco com dados de demonstração
php artisan serve                      # back-end em http://localhost:8000
npm run dev                            # front-end com Vite (hot reload)
php artisan test                       # testes
./vendor/bin/pint                      # formatação (PSR-12)
php artisan route:list                 # conferir rotas
```

## Convenções

- Código, nomes de classes, tabelas e colunas em **inglês** no padrão Laravel (`projects`, `tasks`, `project_user`). Textos de interface, mensagens de validação, comentários e documentação em **português do Brasil**.
- Controllers finos: validação em `FormRequest`, autorização em `Policy`, regra de negócio em `app/Services`, acesso a dados via Eloquent.
- Nenhuma consulta SQL em Blade ou controller; relacionamentos via Eloquent com `with()` para evitar N+1.
- Toda alteração de esquema por migration versionada; nunca editar migration já aplicada em outro ambiente.
- Chaves estrangeiras com `constrained()` e `cascadeOnDelete()` ou `restrictOnDelete()` explícitos.
- Commits em português, no imperativo, curtos (`Adiciona quadro Kanban`).

## Regras de segurança

- Nunca commitar `.env`, chaves ou dados reais. Usar apenas `.env.example`.
- Proteger todas as rotas de projeto com `auth` e Policy; um usuário nunca acessa projeto do qual não é membro.
- Usar proteção CSRF padrão do Laravel em todos os formulários e requisições AJAX.

## Definição de pronto

Uma tarefa só está pronta quando: roda localmente com `migrate:fresh --seed`, passa em `php artisan test`, está formatada com Pint, tem a interface revisada visualmente no navegador e a documentação afetada foi atualizada.

## Documentação

- `README.md`: instalação e uso
- `docs/REQUISITOS.md`: escopo, requisitos e critérios de aceite
- `docs/ARQUITETURA.md`: arquitetura completa do sistema
- `docs/BANCO_DE_DADOS.md`: modelo de dados e integração com o back-end
- `docs/APRESENTACAO.md`: roteiro da apresentação e checklist da demonstração
- `docs/ENTREGA.md`: modelo do e-mail de entrega
- `docs/adr/`: decisões de arquitetura
