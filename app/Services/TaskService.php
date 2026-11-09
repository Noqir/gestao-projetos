<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Regras de negócio de tarefas: criação, edição, movimentação no Kanban,
 * exclusão e comentários. Cada operação grava sua atividade na mesma transação.
 */
class TaskService
{
    public function __construct(private ActivityLogger $activity) {}

    /**
     * Tarefas do projeto agrupadas por coluna, já ordenadas por posição.
     * Usa o índice (project_id, status, position) e carrega o responsável com with().
     *
     * @return array<string, Collection<int, Task>>
     */
    public function board(Project $project, array $filters = []): array
    {
        $tasks = Task::where('project_id', $project->id)
            ->filter($filters)
            ->with('assignee:id,name')
            ->withCount('comments')
            ->orderBy('position')
            ->orderBy('id')
            ->get()
            ->groupBy(fn (Task $task) => $task->status->value);

        return collect(TaskStatus::cases())
            ->mapWithKeys(fn (TaskStatus $status) => [$status->value => $tasks->get($status->value, collect())])
            ->all();
    }

    public function create(Project $project, User $creator, array $data): Task
    {
        return DB::transaction(function () use ($project, $creator, $data) {
            $task = new Task($data);
            $task->project_id = $project->id;
            $task->created_by = $creator->id;
            $task->status ??= TaskStatus::Todo;
            $task->position = $this->nextPosition($project, $task->status);
            $task->save();

            $this->activity->log($project, $creator, 'task.created', $task, ['title' => $task->title]);

            return $task;
        });
    }

    public function update(Task $task, User $actor, array $data): Task
    {
        return DB::transaction(function () use ($task, $actor, $data) {
            $from = $task->status;
            $task->fill($data);

            if ($task->isDirty('status')) {
                $task->position = $this->nextPosition($task->project, $task->status);
            }

            $task->save();
            $this->activity->log($task->project, $actor, 'task.updated', $task, ['title' => $task->title]);

            if ($from !== $task->status) {
                $this->activity->log($task->project, $actor, 'task.moved', $task, [
                    'title' => $task->title, 'from' => $from->value, 'to' => $task->status->value,
                ]);
            }

            return $task;
        });
    }

    /**
     * Move a tarefa para uma coluna (status) e posição, renumerando as colunas
     * afetadas. Chamado pelo arrastar e soltar do Kanban.
     */
    public function moveTo(Task $task, User $actor, TaskStatus $status, ?int $position = null): Task
    {
        return DB::transaction(function () use ($task, $actor, $status, $position) {
            $from = $task->status;

            // Tarefas da coluna de destino, bloqueadas até o COMMIT (evita posições duplicadas).
            $column = Task::where('project_id', $task->project_id)
                ->where('status', $status)
                ->whereKeyNot($task->id)
                ->orderBy('position')
                ->orderBy('id')
                ->lockForUpdate()
                ->pluck('id');

            $position = min(max($position ?? $column->count(), 0), $column->count());
            $column->splice($position, 0, [$task->id]);

            $task->status = $status;
            $task->position = $position;
            $task->save();

            $this->renumber($column);

            if ($from !== $status) {
                $this->renumber(Task::where('project_id', $task->project_id)
                    ->where('status', $from)->orderBy('position')->orderBy('id')->pluck('id'));

                $this->activity->log($task->project, $actor, 'task.moved', $task, [
                    'title' => $task->title, 'from' => $from->value, 'to' => $status->value,
                ]);
            }

            return $task;
        });
    }

    public function delete(Task $task, User $actor): void
    {
        DB::transaction(function () use ($task, $actor) {
            $this->activity->log($task->project, $actor, 'task.deleted', $task, ['title' => $task->title]);
            $task->delete();
        });
    }

    public function comment(Task $task, User $author, string $body): Comment
    {
        return DB::transaction(function () use ($task, $author, $body) {
            $comment = new Comment(['body' => $body]);
            $comment->task_id = $task->id;
            $comment->user_id = $author->id;
            $comment->save();

            $task->touch();
            $this->activity->log($task->project, $author, 'comment.created', $task, ['title' => $task->title]);

            return $comment;
        });
    }

    /** Carrega tudo o que a página de detalhes precisa, em poucas consultas. */
    public function details(Task $task): Task
    {
        return $task->load([
            'project.members:id,name',
            'assignee:id,name',
            'creator:id,name',
            'comments.author:id,name',
        ]);
    }

    private function nextPosition(Project $project, TaskStatus $status): int
    {
        return (int) Task::where('project_id', $project->id)->where('status', $status)->max('position') + 1;
    }

    /**
     * Grava posições 0..n-1 na ordem recebida, alterando só o que mudou.
     * toBase(): reordenar não conta como edição, então updated_at não é tocado.
     */
    private function renumber(Collection $ids): void
    {
        $current = Task::whereKey($ids)->pluck('position', 'id');

        foreach ($ids->values() as $index => $id) {
            if ((int) $current[$id] !== $index) {
                Task::whereKey($id)->toBase()->update(['position' => $index]);
            }
        }
    }
}
