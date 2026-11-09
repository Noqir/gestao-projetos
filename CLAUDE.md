# CLAUDE.md

@AGENTS.md

## Instruções específicas do Claude Code

- Responda sempre em **português do Brasil**, inclusive resumos, mensagens de commit, comentários e documentação. Identificadores de código seguem o padrão Laravel em inglês (ver `AGENTS.md`).
- Antes de implementar qualquer funcionalidade nova, leia `docs/REQUISITOS.md` e `docs/ARQUITETURA.md`. Se a mudança alterar camadas, fluxo de dados ou esquema do banco, atualize `docs/ARQUITETURA.md` e `docs/BANCO_DE_DADOS.md` na mesma tarefa.
- Trabalhe em incrementos pequenos e verificáveis: migration, model, policy, request, service, controller, view, teste. Rode `php artisan test` ao final de cada incremento.
- Ao concluir uma funcionalidade, abra o sistema no navegador e confirme o fluxo na prática; não declare pronto apenas porque os testes passaram.
- Mantenha a interface visualmente consistente: use os componentes Blade em `resources/views/components` e os tokens de cor definidos em `tailwind.config.js` em vez de estilos soltos.
- Não adicione dependências sem necessidade. Se precisar, registre a justificativa em `docs/adr/`.
- Não gere código de apresentação (slides) neste repositório; a apresentação é montada à parte com base em `docs/APRESENTACAO.md`.
- Em dúvida de escopo, priorize o que aparece na demonstração de 10 minutos (ver `docs/APRESENTACAO.md`).

## Comandos personalizados

- `/demo`: prepara o ambiente de demonstração (banco limpo, dados de exemplo, servidor no ar) e valida o roteiro.
- `/verificar-arquitetura`: compara o código com `docs/ARQUITETURA.md` e lista divergências.

## Mapa do repositório

Ver a seção 8 de `docs/ARQUITETURA.md` (estrutura real de diretórios). Resumo:

```
app/
  Enums/                TaskStatus, TaskPriority, ProjectRole
  Http/Controllers/     Dashboard, Project, Member, Task, Comment, Activity (finos)
  Http/Requests/        Validação; authorize() consulta a Policy
  Models/               User, Project, Task, Comment, ActivityLog
  Policies/             ProjectPolicy, TaskPolicy
  Services/             ProjectService, TaskService, DashboardService, ActivityLogger
  View/Composers/       SidebarComposer
database/               migrations, factories, seeders (DemoSeeder)
lang/pt_BR/             Mensagens em português
resources/views/        layouts, components, dashboard, projects, tasks, errors
resources/js/app.js     Alpine: tema, avisos e Kanban (SortableJS)
routes/web.php          Rotas web protegidas por auth
tests/Feature|Unit/     Testes (Pest)
docs/                   Documentação
```
