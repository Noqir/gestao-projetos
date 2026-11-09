<?php

namespace Database\Seeders;

use App\Enums\ProjectRole;
use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Dados de demonstração: 6 usuários, 4 projetos (1 arquivado), 30 tarefas,
 * comentários e histórico de atividades. Datas relativas a hoje, para que
 * sempre existam tarefas atrasadas e prazos próximos no painel.
 *
 * O projeto "Campanha de Marketing Q4" NÃO inclui admin@demo.test,
 * para demonstrar o bloqueio 403 a não membros.
 */
class DemoSeeder extends Seeder
{
    /** @var array<string, User> */
    private array $users = [];

    public function run(): void
    {
        $password = Hash::make('password');

        foreach ([
            'ana' => ['Ana Ribeiro', 'admin@demo.test'],
            'bruno' => ['Bruno Costa', 'membro@demo.test'],
            'carla' => ['Carla Souza', 'carla@demo.test'],
            'rafael' => ['Rafael Lima', 'rafael@demo.test'],
            'beatriz' => ['Beatriz Rocha', 'beatriz@demo.test'],
            'lucas' => ['Lucas Andrade', 'lucas@demo.test'],
        ] as $key => [$name, $email]) {
            $this->users[$key] = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ])->forceFill(['email_verified_at' => now()]);
            $this->users[$key]->save();
        }

        $this->project(
            owner: 'ana',
            members: ['bruno', 'carla', 'rafael'],
            name: 'Lançamento do App Mobile',
            description: 'Primeira versão do aplicativo para iOS e Android: login, onboarding, perfil e notificações.',
            dueInDays: 30,
            startedDaysAgo: 24,
            tasks: [
                // [título, status, prioridade, responsável, prazo (dias a partir de hoje), descrição]
                ['Definir ícones da tela inicial', 'todo', 'low', 'carla', 6, 'Conjunto de ícones para os atalhos da home, nos tamanhos exigidos pelas lojas.'],
                ['Integrar notificações push', 'todo', 'high', 'rafael', 10, 'Integração com o serviço de push para lembretes de tarefas.'],
                ['Escrever termos de uso', 'todo', 'medium', 'bruno', -2, 'Texto revisado pelo jurídico, em linguagem simples.'],
                ['Configurar analytics', 'todo', 'low', null, null, null],
                ['Implementar tela de login', 'doing', 'high', 'rafael', 2, 'Login com e-mail e senha, mensagens de erro em português e opção "lembrar-me".'],
                ['Criar fluxo de onboarding', 'doing', 'medium', 'carla', 5, 'Três telas de boas-vindas apresentando os recursos principais.'],
                ['Ajustar API de perfil', 'doing', 'high', 'ana', -1, 'Endpoint de atualização de perfil retornando 500 ao enviar foto.'],
                ['Revisar textos da loja de apps', 'review', 'medium', 'bruno', 3, 'Descrição curta, descrição longa e notas da versão.'],
                ['Testes de acessibilidade', 'review', 'high', 'carla', 1, 'Contraste, tamanhos de toque e leitores de tela.'],
                ['Protótipo navegável no Figma', 'done', 'high', 'carla', -10, null],
                ['Definir paleta e tipografia', 'done', 'medium', 'carla', -12, null],
                ['Configurar CI do aplicativo', 'done', 'medium', 'rafael', -7, 'Build e testes automáticos a cada push.'],
                ['Levantamento de requisitos', 'done', 'high', 'ana', -20, null],
                ['Pesquisa com usuários', 'done', 'low', 'bruno', -15, 'Entrevistas com 8 usuários do sistema atual.'],
            ],
            comments: [
                'Implementar tela de login' => [
                    ['rafael', 'Validação dos campos pronta. Falta tratar o erro de conexão.'],
                    ['ana', 'Ótimo! Lembre de manter as mensagens em português.'],
                ],
                'Testes de acessibilidade' => [
                    ['carla', 'O contraste dos botões secundários está abaixo de 4.5:1.'],
                    ['bruno', 'Posso ajustar os textos alternativos ainda hoje.'],
                ],
                'Escrever termos de uso' => [
                    ['ana', 'Bruno, consegue priorizar? O prazo venceu.'],
                ],
                'Ajustar API de perfil' => [
                    ['rafael', 'O problema é o limite de upload do servidor. Sugiro aumentar para 5 MB.'],
                ],
            ],
        );

        $this->project(
            owner: 'ana',
            members: ['bruno', 'beatriz'],
            name: 'Redesign do Site Institucional',
            description: 'Nova identidade visual, home reformulada e páginas mais rápidas.',
            dueInDays: 45,
            startedDaysAgo: 18,
            tasks: [
                ['Mapear páginas atuais', 'todo', 'medium', 'beatriz', 4, null],
                ['Migrar blog para o novo layout', 'todo', 'low', 'bruno', 20, null],
                ['Wireframes da home', 'doing', 'high', 'beatriz', 3, 'Versões desktop e mobile, com hierarquia de conteúdo aprovada.'],
                ['Otimizar imagens', 'doing', 'low', 'ana', -3, 'Converter para WebP e aplicar carregamento sob demanda.'],
                ['Nova página de contato', 'review', 'medium', 'bruno', 6, null],
                ['Briefing com a diretoria', 'done', 'high', 'ana', -14, null],
                ['Benchmark de concorrentes', 'done', 'medium', 'beatriz', -9, null],
                ['Definir arquitetura de informação', 'done', 'high', 'beatriz', -5, null],
            ],
            comments: [
                'Wireframes da home' => [
                    ['beatriz', 'Subi a versão 2, sem o carrossel. Ficou bem mais limpa.'],
                    ['ana', 'Aprovado. Pode seguir para o layout final.'],
                ],
            ],
        );

        $this->project(
            owner: 'bruno',
            members: ['lucas', 'carla'],
            name: 'Campanha de Marketing Q4',
            description: 'Campanha de fim de ano: vídeo, redes sociais e landing page.',
            dueInDays: 20,
            startedDaysAgo: 12,
            tasks: [
                ['Roteiro do vídeo', 'todo', 'high', 'lucas', 5, null],
                ['Posts para redes sociais', 'doing', 'medium', 'carla', 2, null],
                ['Landing page da campanha', 'review', 'high', 'bruno', 1, null],
                ['Definir público-alvo', 'done', 'medium', 'bruno', -6, null],
                ['Orçamento de mídia', 'done', 'high', 'lucas', -4, null],
            ],
        );

        $archived = $this->project(
            owner: 'ana',
            members: ['rafael'],
            name: 'Migração de Servidores',
            description: 'Migração da infraestrutura antiga para a nuvem. Concluído.',
            dueInDays: -30,
            startedDaysAgo: 60,
            tasks: [
                ['Inventário de servidores', 'done', 'medium', 'rafael', -50, null],
                ['Migrar banco de dados', 'done', 'high', 'rafael', -40, null],
                ['Desligar servidores antigos', 'done', 'low', 'ana', -32, null],
            ],
        );
        $archived->update(['archived' => true]);
        $this->log($archived, 'ana', 'project.archived', $archived, ['title' => $archived->name], now()->subDays(29));
    }

    /**
     * @param  list<string>  $members
     * @param  list<array{0: string, 1: string, 2: string, 3: ?string, 4: ?int, 5: ?string}>  $tasks
     * @param  array<string, list<array{0: string, 1: string}>>  $comments
     */
    private function project(string $owner, array $members, string $name, string $description, int $dueInDays, int $startedDaysAgo, array $tasks, array $comments = []): Project
    {
        $start = now()->subDays($startedDaysAgo)->setTime(9, 0);

        $project = new Project([
            'name' => $name,
            'description' => $description,
            'due_date' => today()->addDays($dueInDays),
        ]);
        $project->owner_id = $this->users[$owner]->id;
        $project->created_at = $start;
        $project->save();

        $project->members()->attach($this->users[$owner]->id, ['role' => ProjectRole::Owner->value, 'created_at' => $start]);
        $this->log($project, $owner, 'project.created', $project, ['title' => $name], $start);

        foreach ($members as $i => $member) {
            $at = $start->copy()->addHours(1 + $i);
            $project->members()->attach($this->users[$member]->id, ['role' => ProjectRole::Member->value, 'created_at' => $at]);
            $this->log($project, $owner, 'member.added', $this->users[$member], ['member' => $this->users[$member]->name], $at);
        }

        $positions = [];
        $team = array_merge([$owner], $members);

        foreach ($tasks as $i => [$title, $status, $priority, $assignee, $dueIn, $taskDescription]) {
            $creator = $team[$i % count($team)];
            $createdAt = $start->copy()->addDays(min($i, $startedDaysAgo - 1))->addHours(2);

            $task = new Task([
                'title' => $title,
                'description' => $taskDescription,
                'status' => $status,
                'priority' => $priority,
                'assignee_id' => $assignee ? $this->users[$assignee]->id : null,
                'due_date' => $dueIn !== null ? today()->addDays($dueIn) : null,
                'position' => $positions[$status] = ($positions[$status] ?? -1) + 1,
            ]);
            $task->project_id = $project->id;
            $task->created_by = $this->users[$creator]->id;
            $task->created_at = $createdAt;
            $task->updated_at = $createdAt;
            $task->save();

            $this->log($project, $creator, 'task.created', $task, ['title' => $title], $createdAt);

            // Histórico de movimentação coerente com o status atual
            $path = ['todo', 'doing', 'review', 'done'];
            $steps = array_search($status, $path, true);
            $movedAt = $createdAt->copy();
            for ($s = 1; $s <= $steps; $s++) {
                $movedAt = Carbon::parse(min($movedAt->copy()->addDays(1)->addHours(3)->timestamp, now()->subHours(6 - $s)->timestamp));
                $this->log($project, $assignee ?? $owner, 'task.moved', $task, ['title' => $title, 'from' => $path[$s - 1], 'to' => $path[$s]], $movedAt);
            }

            foreach ($comments[$title] ?? [] as $c => [$author, $body]) {
                $at = now()->subHours(20 - $c * 3)->subMinutes(17 * $i);
                $comment = new Comment(['body' => $body]);
                $comment->task_id = $task->id;
                $comment->user_id = $this->users[$author]->id;
                $comment->created_at = $at;
                $comment->updated_at = $at;
                $comment->save();
                $this->log($project, $author, 'comment.created', $task, ['title' => $title], $at);
            }
        }

        return $project;
    }

    private function log(Project $project, string $user, string $action, object $subject, array $properties, Carbon $at): void
    {
        ActivityLog::create([
            'project_id' => $project->id,
            'user_id' => $this->users[$user]->id,
            'action' => $action,
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'properties' => $properties,
            'created_at' => $at,
        ]);
    }
}
