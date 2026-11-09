<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Regra base: só membros veem o projeto; só o proprietário edita,
 * arquiva, exclui e gerencia membros; membros criam tarefas.
 */
class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): Response
    {
        return $project->hasMember($user)
            ? Response::allow()
            : Response::deny('Você não é membro deste projeto.');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Project $project): Response
    {
        return $project->isOwnedBy($user)
            ? Response::allow()
            : Response::deny('Apenas o proprietário pode alterar este projeto.');
    }

    public function archive(User $user, Project $project): Response
    {
        return $this->update($user, $project);
    }

    public function delete(User $user, Project $project): Response
    {
        return $this->update($user, $project);
    }

    public function manageMembers(User $user, Project $project): Response
    {
        return $project->isOwnedBy($user)
            ? Response::allow()
            : Response::deny('Apenas o proprietário pode gerenciar membros.');
    }

    /** Membros criam tarefas, desde que o projeto não esteja arquivado. */
    public function createTask(User $user, Project $project): Response
    {
        if ($project->archived) {
            return Response::deny('Projeto arquivado: reative-o para criar tarefas.');
        }

        return $this->view($user, $project);
    }
}
