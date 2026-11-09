# ADR 0001: Escolha da stack tecnológica

- **Status:** Aceita (versão do framework substituída pelo ADR 0002)
- **Data:** 09/11/2026

## Contexto

O enunciado permite framework (Laravel) e uso de IA, exige banco de dados e funcionamento demonstrável em 10 minutos. O prazo é curto (apresentação em 13/11/2026) e a equipe tem seis integrantes. A arquitetura precisa ser explicável e a estética é muito avaliada.

## Decisão

Laravel 11 (PHP 8.3; atualizado para Laravel 13 pelo ADR 0002) como monólito MVC com renderização no servidor (Blade), MySQL 8 como banco, Eloquent como ORM, Tailwind CSS e Alpine.js no front-end, Vite como empacotador, Breeze para autenticação.

## Alternativas consideradas

- **SPA (React ou Vue) com API REST:** mais partes móveis, mais tempo de integração e risco de falha na demonstração.
- **Livewire:** reduz JavaScript, mas esconde parte do fluxo cliente-servidor que precisa ser explicado.
- **PostgreSQL ou SQLite:** viáveis, mas MySQL é o banco já trabalhado na disciplina de banco de dados.
- **Redis para sessão e cache:** serviço extra desnecessário; o próprio MySQL atende.

## Consequências

Positivas: fluxo de requisição claro e fácil de explicar; segurança por padrão (CSRF, hash, prepared statements); produtividade alta; instalação simples.
Negativas: menos interatividade em tempo real; Kanban depende de JavaScript pontual (SortableJS + fetch).

## Revisão

Reavaliar apenas se surgir requisito de tempo real ou API pública.
