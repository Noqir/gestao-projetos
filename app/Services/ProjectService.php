<?php

namespace App\Services;

use App\Enums\ProjectRole;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Regras de negócio de projetos e membros.
 * Operações compostas rodam em DB::transaction junto com o registro de atividade.
 */
class ProjectService
{
    public function __construct(private ActivityLogger $activity) {}

    /** Projetos do usuário (ativos ou arquivados) com progresso e membros, sem N+1. */
    public function listFor(User $user, bool $archived = false): Collection
    {
        return Project::visibleTo($user)
            ->where('archived', $archived)
            ->withProgress()
            ->with(['members:id,name'])
            ->orderByDesc('updated_at')
            ->get();
    }

    /** @return array{active: int, archived: int} */
    public function countsFor(User $user): array
    {
        $counts = Project::visibleTo($user)
            ->selectRaw('archived, COUNT(*) as total')
            ->groupBy('archived')
            ->pluck('total', 'archived');

        return ['active' => (int) ($counts[0] ?? 0), 'archived' => (int) ($counts[1] ?? 0)];
    }

    /** Lista curta para a barra lateral. */
    public function sidebarFor(User $user): Collection
    {
        return Project::visibleTo($user)->active()->orderBy('name')->limit(8)->get(['id', 'name']);
    }

    /** Cria o projeto e vincula o criador como proprietário (atômico). */
    public function create(User $owner, array $data): Project
    {
        return DB::transaction(function () use ($owner, $data) {
            $project = new Project($data);
            $project->owner_id = $owner->id;
            $project->save();

            $project->members()->attach($owner->id, ['role' => ProjectRole::Owner->value]);
            $this->activity->log($project, $owner, 'project.created', $project, ['title' => $project->name]);

            return $project;
        });
    }

    public function update(Project $project, User $actor, array $data): Project
    {
        return DB::transaction(function () use ($project, $actor, $data) {
            $project->update($data);
            $this->activity->log($project, $actor, 'project.updated', $project, ['title' => $project->name]);

            return $project;
        });
    }

    public function setArchived(Project $project, User $actor, bool $archived): Project
    {
        return DB::transaction(function () use ($project, $actor, $archived) {
            $project->update(['archived' => $archived]);
            $this->activity->log($project, $actor, $archived ? 'project.archived' : 'project.restored', $project, ['title' => $project->name]);

            return $project;
        });
    }

    /** Tarefas, comentários, vínculos e histórico saem por ON DELETE CASCADE. */
    public function delete(Project $project): void
    {
        $project->delete();
    }

    /** Membros com papel e quantidade de tarefas abertas atribuídas neste projeto. */
    public function membersOf(Project $project): Collection
    {
        return $project->members()
            ->withCount(['assignedTasks as open_tasks_count' => fn ($q) => $q
                ->where('project_id', $project->id)
                ->where('status', '!=', 'done')])
            ->orderByRaw('CASE WHEN project_user.role = ? THEN 0 ELSE 1 END', [ProjectRole::Owner->value])
            ->orderBy('name')
            ->get();
    }

    /** Adiciona um usuário já cadastrado, pelo e-mail. */
    public function addMember(Project $project, User $actor, string $email): User
    {
        $user = User::where('email', $email)->firstOrFail();

        if ($project->hasMember($user)) {
            throw ValidationException::withMessages(['email' => "{$user->name} já faz parte deste projeto."]);
        }

        DB::transaction(function () use ($project, $actor, $user) {
            $project->members()->attach($user->id, ['role' => ProjectRole::Member->value]);
            $this->activity->log($project, $actor, 'member.added', $user, ['member' => $user->name]);
        });

        return $user;
    }

    /** Remove o membro e libera as tarefas que estavam com ele neste projeto. */
    public function removeMember(Project $project, User $actor, User $member): void
    {
        if ($project->isOwnedBy($member)) {
            throw ValidationException::withMessages(['member' => 'O proprietário não pode ser removido do projeto.']);
        }

        DB::transaction(function () use ($project, $actor, $member) {
            $project->members()->detach($member->id);
            $project->tasks()->where('assignee_id', $member->id)->update(['assignee_id' => null]);
            $this->activity->log($project, $actor, 'member.removed', $member, ['member' => $member->name]);
        });
    }

    /** Linha do tempo paginada do projeto. */
    public function activityOf(Project $project, int $perPage = 25): LengthAwarePaginator
    {
        return ActivityLog::where('project_id', $project->id)
            ->with('user:id,name')
            ->latest('created_at')
            ->latest('id')
            ->paginate($perPage);
    }
}
