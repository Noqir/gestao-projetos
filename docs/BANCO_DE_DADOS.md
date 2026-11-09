# Modelo de Dados

Banco MySQL 8, charset `utf8mb4`, collation `utf8mb4_unicode_ci`, engine InnoDB (chaves estrangeiras e transações). Esquema criado por migrations.

## Diagrama entidade-relacionamento

```mermaid
erDiagram
    users ||--o{ projects : "é proprietário (owner_id)"
    users }o--o{ projects : "participa (project_user)"
    projects ||--o{ tasks : contém
    users ||--o{ tasks : "é responsável (assignee_id)"
    users ||--o{ tasks : "cria (created_by)"
    tasks ||--o{ comments : recebe
    users ||--o{ comments : escreve
    projects ||--o{ activity_logs : registra
    users ||--o{ activity_logs : executa

    users {
        bigint id PK
        varchar name
        varchar email UK
        varchar password
        timestamp created_at
    }
    projects {
        bigint id PK
        bigint owner_id FK
        varchar name
        text description
        date due_date
        boolean archived
        timestamp created_at
    }
    project_user {
        bigint project_id FK
        bigint user_id FK
        enum role "owner, member"
    }
    tasks {
        bigint id PK
        bigint project_id FK
        bigint assignee_id FK "nullable"
        bigint created_by FK
        varchar title
        text description
        enum status "todo, doing, review, done"
        enum priority "low, medium, high"
        date due_date
        int position
        timestamp created_at
    }
    comments {
        bigint id PK
        bigint task_id FK
        bigint user_id FK
        text body
        timestamp created_at
    }
    activity_logs {
        bigint id PK
        bigint project_id FK
        bigint user_id FK
        varchar action
        varchar subject_type
        bigint subject_id
        timestamp created_at
    }
```

## Regras de integridade

| Relação | Ação ao excluir |
|---|---|
| `projects.owner_id -> users.id` | RESTRICT |
| `project_user.project_id -> projects.id` | CASCADE |
| `project_user.user_id -> users.id` | CASCADE |
| `tasks.project_id -> projects.id` | CASCADE |
| `tasks.assignee_id -> users.id` | SET NULL |
| `comments.task_id -> tasks.id` | CASCADE |
| `activity_logs.project_id -> projects.id` | CASCADE |

Restrições adicionais: `users.email` único; `(project_id, user_id)` chave primária composta em `project_user`.

## Índices

`tasks (project_id, status, position)` para montar o Kanban; `tasks (assignee_id)`; `tasks (due_date)` para tarefas atrasadas; `comments (task_id)`; `activity_logs (project_id, created_at)`.

## Mapeamento Eloquent

| Model | Relacionamentos |
|---|---|
| `User` | `ownedProjects` hasMany, `projects` belongsToMany (pivot `role`), `assignedTasks` hasMany |
| `Project` | `owner` belongsTo, `members` belongsToMany, `tasks` hasMany, `activities` hasMany |
| `Task` | `project` belongsTo, `assignee` belongsTo, `creator` belongsTo, `comments` hasMany |
| `Comment` | `task` belongsTo, `author` belongsTo |
| `ActivityLog` | `project` belongsTo, `user` belongsTo |

## Consultas de destaque (para a demonstração)

O painel usa agregações em uma única consulta por indicador: contagem de tarefas por status (`GROUP BY status`), tarefas atrasadas (`due_date < CURDATE() AND status <> 'done'`) e carga por responsável (`JOIN users ... GROUP BY assignee_id`). Executadas via Query Builder em `DashboardService`.

## Dados de demonstração

`DatabaseSeeder` cria 2 usuários, 2 projetos, cerca de 20 tarefas distribuídas entre os status, comentários e atividades, para que o painel e o Kanban já apareçam preenchidos.
