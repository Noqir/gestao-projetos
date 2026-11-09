<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Tarefas herdam a regra do projeto: membros veem, editam, movem e comentam.
 * Excluir é permitido ao proprietário do projeto ou a quem criou a tarefa.
 * Projetos arquivados ficam somente leitura.
 */
class TaskPolicy
{
    public function view(User $user, Task $task): Response
    {
        return $task->project->hasMember($user)
            ? Response::allow()
            : Response::deny('Você não é membro do projeto desta tarefa.');
    }

    public function update(User $user, Task $task): Response
    {
        if ($task->project->archived) {
            return Response::deny('Projeto arquivado: as tarefas estão somente para leitura.');
        }

        return $this->view($user, $task);
    }

    public function delete(User $user, Task $task): Response
    {
        $response = $this->update($user, $task);

        if ($response->denied()) {
            return $response;
        }

        return $task->project->isOwnedBy($user) || $task->created_by === $user->id
            ? Response::allow()
            : Response::deny('Apenas o proprietário do projeto ou o autor da tarefa podem excluí-la.');
    }

    public function comment(User $user, Task $task): Response
    {
        return $this->update($user, $task);
    }
}
