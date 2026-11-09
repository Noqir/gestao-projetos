<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Indicadores do painel. Cada indicador é UMA consulta agregada no MySQL
 * (COUNT/SUM + GROUP BY) feita pelo Query Builder, em vez de contar no PHP.
 * Considera apenas os projetos ativos dos quais o usuário é membro.
 */
class DashboardService
{
    /** @return array<string, mixed> */
    public function for(User $user): array
    {
        $projectIds = Project::visibleTo($user)->active()->pluck('id');

        $byStatus = $this->tasksByStatus($projectIds);
        $total = array_sum($byStatus);

        return [
            'hasProjects' => $projectIds->isNotEmpty(),
            'byStatus' => $byStatus,
            'total' => $total,
            'completion' => $total ? (int) round($byStatus[TaskStatus::Done->value] * 100 / $total) : 0,
            'overdue' => $this->overdueCount($projectIds),
            'workload' => $this->workload($projectIds),
            'projects' => $this->projectsProgress($projectIds),
            'myTasks' => $this->openTasksOf($user, $projectIds),
            'activity' => $this->recentActivity($projectIds),
        ];
    }

    /**
     * SELECT status, COUNT(*) AS total FROM tasks WHERE project_id IN (...) GROUP BY status
     *
     * @return array<string, int> todas as colunas, inclusive as vazias
     */
    public function tasksByStatus(Collection $projectIds): array
    {
        $counts = DB::table('tasks')
            ->whereIn('project_id', $projectIds)
            ->select('status', DB::raw('COUNT(*) AS total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(TaskStatus::cases())
            ->mapWithKeys(fn (TaskStatus $s) => [$s->value => (int) ($counts[$s->value] ?? 0)])
            ->all();
    }

    /** SELECT COUNT(*) FROM tasks WHERE ... AND due_date < ? AND status <> 'done' */
    public function overdueCount(Collection $projectIds): int
    {
        return Task::whereIn('project_id', $projectIds)->overdue()->count();
    }

    /**
     * Carga por responsável:
     * SELECT users.id, users.name, SUM(...) AS open, SUM(...) AS done
     * FROM tasks JOIN users ON users.id = tasks.assignee_id ... GROUP BY users.id, users.name
     */
    public function workload(Collection $projectIds): Collection
    {
        return DB::table('tasks')
            ->join('users', 'users.id', '=', 'tasks.assignee_id')
            ->whereIn('tasks.project_id', $projectIds)
            ->select('users.id', 'users.name')
            ->selectRaw('SUM(CASE WHEN tasks.status <> ? THEN 1 ELSE 0 END) AS open_tasks', [TaskStatus::Done->value])
            ->selectRaw('SUM(CASE WHEN tasks.status = ? THEN 1 ELSE 0 END) AS done_tasks', [TaskStatus::Done->value])
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('open_tasks')
            ->orderBy('users.name')
            ->get()
            ->map(function (object $row) {
                $row->open_tasks = (int) $row->open_tasks;
                $row->done_tasks = (int) $row->done_tasks;

                return $row;
            });
    }

    public function projectsProgress(Collection $projectIds): Collection
    {
        return Project::whereIn('id', $projectIds)
            ->withProgress()
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();
    }

    public function openTasksOf(User $user, Collection $projectIds): Collection
    {
        return Task::whereIn('project_id', $projectIds)
            ->where('assignee_id', $user->id)
            ->where('status', '!=', TaskStatus::Done)
            ->with('project:id,name')
            ->orderByRaw('due_date IS NULL')
            ->orderBy('due_date')
            ->limit(6)
            ->get();
    }

    public function recentActivity(Collection $projectIds): Collection
    {
        return ActivityLog::whereIn('project_id', $projectIds)
            ->with(['user:id,name', 'project:id,name'])
            ->latest('created_at')
            ->latest('id')
            ->limit(8)
            ->get();
    }
}
