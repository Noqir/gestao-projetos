# Requisitos

## Contexto do enunciado

Trabalho Avaliativo 3, Programação de Sistemas Web (Afya, prof. Gabriel Cardoso). Tema da Equipe 1: **Plataforma de Gestão de Projetos**.

Regras de desenvolvimento: framework permitido (Laravel), uso de IA permitido, banco de dados obrigatório, execução local ou remota.

Regras de apresentação: 10 minutos por equipe; expor em detalhes a arquitetura de todo o sistema, não o código; mostrar o sistema funcionando (obrigatório); estética muito avaliada; todos os membros falam. Critérios: estética, funcionalidade, apresentação, com rigor maior que o habitual.

Prazos e nota: 13/11/2026 (máx. 10), 03/12/2026 (máx. 5), a partir de 04/12/2026 (máx. 2,5). Entrega: somente a apresentação, por e-mail (ver `docs/ENTREGA.md`).

## Requisitos funcionais

| ID | Requisito | Prioridade |
|---|---|---|
| RF01 | Cadastro, login e logout de usuários | Essencial |
| RF02 | Criar, editar, listar e arquivar projetos | Essencial |
| RF03 | Adicionar e remover membros de um projeto | Essencial |
| RF04 | Criar, editar e excluir tarefas (título, descrição, responsável, prioridade, prazo) | Essencial |
| RF05 | Quadro Kanban com colunas por status e arrastar e soltar | Essencial |
| RF06 | Painel com indicadores (por status, atrasadas, por responsável) | Essencial |
| RF07 | Comentar em tarefas | Importante |
| RF08 | Histórico de atividades do projeto | Importante |
| RF09 | Filtrar tarefas por responsável, prioridade e busca textual | Desejável |
| RF10 | Alternância de tema claro e escuro | Desejável |

## Requisitos não funcionais

- **RNF01 Desempenho:** páginas principais em menos de 1 s localmente, sem consultas N+1.
- **RNF02 Segurança:** senhas com hash, CSRF, prepared statements, autorização por Policy.
- **RNF03 Usabilidade e estética:** interface responsiva, consistente, com estados vazios, mensagens de sucesso e erro em português.
- **RNF04 Portabilidade:** instalação em até 5 comandos documentados no README.
- **RNF05 Demonstrabilidade:** `migrate:fresh --seed` deixa o sistema pronto para demonstração.

## Critérios de aceite resumidos

- RF05: ao arrastar um cartão para outra coluna, o status persiste no banco e permanece após recarregar a página.
- RF03: um usuário que não é membro recebe 403 ao acessar o projeto.
- RF06: os números do painel conferem com a contagem real de tarefas no banco.
- RF04: tarefa sem título é rejeitada com mensagem em português.

## Fora do escopo

E-mail de convite, notificações em tempo real, API pública, anexos, integração com serviços externos.

## Desenvolvimento

O sistema é implementado integralmente pelo Claude Code a partir desta documentação. Não há divisão de desenvolvimento por integrante. Todos os seis integrantes devem, porém, conhecer a arquitetura e falar uma parte na apresentação (ver `docs/APRESENTACAO.md`).
