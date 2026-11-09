<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Database\Factories\ActivityLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Registro de atividade do projeto. Gravado apenas pelo ActivityLogger.
 */
#[Fillable(['project_id', 'user_id', 'action', 'subject_type', 'subject_id', 'properties', 'created_at'])]
class ActivityLog extends Model
{
    /** @use HasFactory<ActivityLogFactory> */
    use HasFactory;

    /** A tabela só tem created_at (registro imutável). */
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /** Frase em português que descreve a ação (sem o nome do autor). */
    public function message(): string
    {
        $p = $this->properties ?? [];
        $title = $p['title'] ?? '';
        $status = fn (?string $value) => $value ? TaskStatus::tryFrom($value)?->label() ?? $value : '';

        return match ($this->action) {
            'project.created' => "criou o projeto “{$title}”",
            'project.updated' => 'atualizou os dados do projeto',
            'project.archived' => 'arquivou o projeto',
            'project.restored' => 'reativou o projeto',
            'member.added' => 'adicionou '.($p['member'] ?? 'um membro').' ao projeto',
            'member.removed' => 'removeu '.($p['member'] ?? 'um membro').' do projeto',
            'task.created' => "criou a tarefa “{$title}”",
            'task.updated' => "editou a tarefa “{$title}”",
            'task.moved' => "moveu “{$title}” de {$status($p['from'] ?? null)} para {$status($p['to'] ?? null)}",
            'task.deleted' => "excluiu a tarefa “{$title}”",
            'comment.created' => "comentou em “{$title}”",
            default => $this->action,
        };
    }

    /** Ícone e cor do marcador na linha do tempo. */
    public function icon(): array
    {
        return match ($this->action) {
            'task.moved' => ['move', 'bg-sky-500'],
            'task.created', 'project.created' => ['plus', 'bg-brand-500'],
            'task.deleted' => ['trash', 'bg-rose-500'],
            'comment.created' => ['chat', 'bg-violet-500'],
            'member.added' => ['user-plus', 'bg-emerald-500'],
            'member.removed' => ['user-minus', 'bg-rose-500'],
            'project.archived' => ['archive', 'bg-slate-500'],
            default => ['pencil', 'bg-amber-500'],
        };
    }
}
