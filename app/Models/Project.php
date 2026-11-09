<?php

namespace App\Models;

use App\Enums\ProjectRole;
use App\Enums\TaskStatus;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'due_date', 'archived'])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'archived' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** Membros (inclui o proprietário), via project_user com o papel. */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->latest('created_at')->latest('id');
    }

    /** Apenas projetos dos quais o usuário é membro. */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->whereHas('members', fn (Builder $q) => $q->whereKey($user->id));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('archived', false);
    }

    /** Conta tarefas totais e concluídas em subconsultas (evita N+1 na listagem). */
    public function scopeWithProgress(Builder $query): Builder
    {
        return $query->withCount([
            'tasks',
            'tasks as done_tasks_count' => fn (Builder $q) => $q->where('status', TaskStatus::Done),
        ]);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    /** Verifica pertencimento consultando o pivô (ou a relação já carregada). */
    public function hasMember(User $user): bool
    {
        if ($this->relationLoaded('members')) {
            return $this->members->contains($user);
        }

        return $this->members()->whereKey($user->id)->exists();
    }

    public function roleOf(User $user): ?ProjectRole
    {
        $member = $this->members->firstWhere('id', $user->id);

        return $member ? ProjectRole::from($member->pivot->role) : null;
    }

    /** Percentual concluído; requer scopeWithProgress. */
    public function progress(): int
    {
        $total = (int) ($this->tasks_count ?? 0);

        return $total === 0 ? 0 : (int) round(($this->done_tasks_count ?? 0) * 100 / $total);
    }

    /** Cor de identificação estável derivada do id. */
    public function colorClass(): string
    {
        $colors = ['bg-brand-500', 'bg-sky-500', 'bg-emerald-500', 'bg-amber-500', 'bg-rose-500', 'bg-violet-500', 'bg-teal-500'];

        return $colors[$this->id % count($colors)];
    }

    public function isOverdue(): bool
    {
        return ! $this->archived && $this->due_date?->isPast() && ! $this->due_date->isToday();
    }
}
