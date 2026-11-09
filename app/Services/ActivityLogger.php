<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Único ponto de gravação do histórico de atividades (activity_logs).
 * É chamado pelos Services dentro da mesma transação da operação registrada.
 */
class ActivityLogger
{
    /**
     * @param  array<string, mixed>  $properties  Dados para montar a frase (título, status de/para...)
     */
    public function log(Project $project, ?User $user, string $action, ?Model $subject = null, array $properties = []): ActivityLog
    {
        return ActivityLog::create([
            'project_id' => $project->id,
            'user_id' => $user?->id,
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'properties' => $properties,
            'created_at' => now(),
        ]);
    }
}
