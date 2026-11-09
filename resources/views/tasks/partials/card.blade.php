{{-- Cartão do Kanban. data-status-url é o endpoint PATCH usado pelo arrastar e soltar. --}}
<a href="{{ route('projects.tasks.show', [$project, $task]) }}"
    data-task="{{ $task->id }}"
    data-status="{{ $task->status->value }}"
    data-status-url="{{ route('tasks.status', $task) }}"
    @class([
        'group block select-none rounded-xl border bg-surface p-3.5 shadow-card transition duration-150 hover:border-brand-300 hover:shadow-md dark:hover:border-brand-500/40',
        'cursor-grab active:cursor-grabbing' => $canMove,
        'border-rose-200 dark:border-rose-500/30' => $task->isOverdue(),
        'border-line' => ! $task->isOverdue(),
    ])>
    <div class="mb-2 flex items-center justify-between gap-2">
        <x-badge :color="$task->priority->color()" dot>{{ $task->priority->label() }}</x-badge>
        <x-due-date :date="$task->due_date" :overdue="$task->isOverdue()" :soon="$task->isDueSoon()" />
    </div>

    <h3 @class(['text-sm font-medium leading-snug text-ink', 'line-through decoration-ink-subtle/60' => $task->status === \App\Enums\TaskStatus::Done])>{{ $task->title }}</h3>

    <div class="mt-3 flex items-center justify-between">
        <div class="flex items-center gap-3 text-xs text-ink-subtle">
            @if ($task->comments_count > 0)
                <span class="inline-flex items-center gap-1" title="{{ $task->comments_count }} comentário(s)">
                    <x-icon name="chat" class="size-3.5" />{{ $task->comments_count }}
                </span>
            @endif
            @if ($task->description)
                <x-icon name="document" class="size-3.5" title="Tem descrição" />
            @endif
        </div>
        <x-avatar :user="$task->assignee" size="xs" />
    </div>
</a>
