<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'assignee_id', 'status', 'priority', 'due_date', 'position'])]
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    protected $attributes = [
        'status' => 'todo',
        'priority' => 'medium',
        'position' => 0,
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
            'due_date' => 'date',
            'position' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->oldest();
    }

    /**
     * Prazo vencido e ainda não concluída.
     * Compara a coluna DATE diretamente (sem DATE()) para o MySQL usar o índice tasks(due_date).
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', today()->toDateString())->where('status', '!=', TaskStatus::Done);
    }

    /**
     * Filtros do quadro (RF09): responsável, prioridade e busca textual.
     *
     * @param  array{assignee?: string|null, priority?: string|null, q?: string|null}  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['assignee'] ?? null, fn (Builder $q, $assignee) => $assignee === 'none'
                ? $q->whereNull('assignee_id')
                : $q->where('assignee_id', $assignee))
            ->when($filters['priority'] ?? null, fn (Builder $q, $priority) => $q->where('priority', $priority))
            ->when($filters['q'] ?? null, fn (Builder $q, $term) => $q->where(fn (Builder $w) => $w
                ->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")));
    }

    public function isOverdue(): bool
    {
        return $this->due_date !== null
            && $this->status !== TaskStatus::Done
            && $this->due_date->lt(today());
    }

    public function isDueSoon(): bool
    {
        return $this->due_date !== null
            && $this->status !== TaskStatus::Done
            && $this->due_date->between(today(), today()->addDays(2));
    }
}
