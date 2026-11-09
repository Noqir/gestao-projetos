# ADR 0002: Laravel 13 no lugar do Laravel 11

- **Status:** Aceita
- **Data:** 09/11/2026
- **Substitui parcialmente:** ADR 0001 (apenas a versão do framework)

## Contexto

O ADR 0001 escolheu o Laravel 11. Ao criar o projeto, o Composer recusou instalar qualquer versão 11.x porque todas estão afetadas por avisos de segurança publicados no Packagist e sem correção (o Laravel 11 saiu do período de suporte de segurança). Entre eles: injeção CRLF na regra padrão de validação de e-mail e XSS na página de depuração de erros.

## Decisão

Usar o **Laravel 13** (13.40 na criação do projeto), versão atual e suportada, com Laravel Breeze 2.4 (que declara compatibilidade com 11, 12 e 13). O restante da stack do ADR 0001 permanece: PHP 8.3+, MySQL 8, Blade, Tailwind CSS 3, Alpine.js, Vite, Pest.

## Alternativas consideradas

- **Laravel 11 ignorando os avisos:** fiel ao documento original, mas com vulnerabilidades conhecidas e abertas. Rejeitada.
- **Laravel 12:** também corrigido, mas com fim de suporte de segurança anterior ao 13.

## Consequências

- A estrutura de pastas, o ciclo de requisição, Eloquent, Policies, FormRequests e migrations são os mesmos do Laravel 11; nenhuma parte de `docs/ARQUITETURA.md` muda além da versão.
- `composer audit` não aponta vulnerabilidades na criação do projeto.
