# Roteiro da Apresentação (10 minutos)

Objetivo: prender a atenção, explicar a arquitetura em detalhes (sem mostrar código) e mostrar o sistema funcionando. Todos os seis integrantes falam.

## Princípios

- Arquitetura é o ponto mais importante: reservar cerca de 4 minutos e usar diagramas de `docs/ARQUITETURA.md`.
- Demonstração ao vivo obrigatória: cerca de 3 minutos, com banco já populado e roteiro ensaiado. Ter um vídeo curto de reserva.
- Estética: poucos textos por slide, um diagrama ou imagem por ideia, paleta e tipografia iguais às do sistema, transições discretas.
- Nenhum trecho de código nos slides.

## Cronograma

| Tempo | Bloco | Fala (sugestão) | Conteúdo |
|---|---|---|---|
| 0:00 a 0:45 | Abertura e problema | João Pedro | Tema, equipe, dor que a plataforma resolve |
| 0:45 a 1:30 | Visão geral e funcionalidades | Júlia | Captura da interface, fluxo do usuário |
| 1:30 a 3:00 | Arquitetura em camadas | David | Diagrama geral, MVC, papel de cada camada |
| 3:00 a 4:45 | Banco de dados e comunicação com o back-end | David e Marcelo | ER, PDO, Eloquent, prepared statements, transações, migrations |
| 4:45 a 5:30 | Segurança | João Pedro | Autenticação, Policies, CSRF |
| 5:30 a 8:30 | Demonstração ao vivo | Pedro Henrique e Djalma | Roteiro abaixo |
| 8:30 a 9:30 | Desafios e aprendizados | Marcelo | Decisões, uso de IA, trabalho em equipe |
| 9:30 a 10:00 | Encerramento | Júlia | Resumo, evolução futura, perguntas |

A distribuição das falas é uma sugestão para que os seis integrantes participem; cada um deve estudar a arquitetura completa para responder perguntas sobre qualquer parte. Ajustar livremente.

## Roteiro da demonstração ao vivo

1. Login com `admin@demo.test`.
2. Painel: mostrar indicadores já preenchidos.
3. Abrir o projeto de demonstração e o quadro Kanban.
4. Criar uma tarefa nova (título, responsável, prioridade, prazo).
5. Arrastar a tarefa para "Em andamento" e recarregar a página para provar a persistência.
6. Comentar na tarefa e mostrar o histórico de atividades.
7. Voltar ao painel e mostrar que os números mudaram.
8. Opcional para conectar à arquitetura: mostrar a tabela `tasks` no cliente MySQL com o novo status.
9. Mostrar o bloqueio de acesso: usuário não membro recebe 403. Logado como `admin@demo.test`, abrir `http://localhost:8000/projects/3` ("Campanha de Marketing Q4", da qual a Ana não participa).

Observação: o painel conta apenas projetos **ativos**. Como os prazos do seeder são relativos ao dia em que ele roda, execute `migrate:fresh --seed` no dia da apresentação para manter tarefas "atrasadas" e "para amanhã" coerentes.

## Checklist antes de apresentar

- [ ] `php artisan migrate:fresh --seed` executado
- [ ] `php artisan serve` e `npm run dev` rodando; MySQL ativo
- [ ] Navegador em tela cheia, zoom ajustado, abas desnecessárias fechadas, notificações desligadas
- [ ] Segunda janela com o cliente MySQL aberta na tabela `tasks`
- [ ] Vídeo de reserva da demonstração salvo localmente
- [ ] Slides exportados em PDF e PPTX; cópia no pen drive
- [ ] Ensaio completo cronometrado (meta: 9 min 30 s)
- [ ] Internet de reserva (roteador do celular)

## Slides sugeridos (12 a 14)

1. Capa
2. Problema e proposta
3. Funcionalidades
4. Visão geral da arquitetura
5. Camadas e responsabilidades
6. Ciclo de uma requisição
7. Banco de dados: modelo ER
8. Comunicação banco e back-end
9. Segurança
10. Demonstração (slide de transição)
11. Desafios e aprendizados
12. Evolução futura e agradecimentos

## Identidade visual

Definir e reutilizar no sistema e nos slides: uma cor primária, uma cor de destaque, fonte sem serifa legível (por exemplo Inter), ícones de um único conjunto, e espaçamento generoso. Preferir tema escuro com alto contraste se a sala permitir, com opção clara de reserva.
