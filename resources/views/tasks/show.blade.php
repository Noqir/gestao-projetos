<x-app-layout>
    <x-slot name="title">{{ $task->title }}</x-slot>

    @php $canEdit = auth()->user()->can('update', $task); @endphp

    <div class="mb-6 flex items-center gap-1.5 text-sm text-ink-muted">
        <a href="{{ route('projects.index') }}" class="hover:text-ink">Projetos</a>
        <x-icon name="chevron-right" class="size-3.5 text-ink-subtle" />
        <a href="{{ route('projects.show', $project) }}" class="truncate hover:text-ink">{{ $project->name }}</a>
        <x-icon name="chevron-right" class="size-3.5 text-ink-subtle" />
        <span class="text-ink">Tarefa #{{ $task->id }}</span>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
        <div class="space-y-6">
            <x-card>
                <div class="flex flex-wrap items-center gap-2">
                    <x-badge :color="$task->status->color()" dot>{{ $task->status->label() }}</x-badge>
                    <x-badge :color="$task->priority->color()">Prioridade {{ mb_strtolower($task->priority->label()) }}</x-badge>
                    @if ($task->isOverdue())
                        <x-badge color="rose"><x-icon name="warning" class="size-3.5" />Atrasada</x-badge>
                    @endif
                </div>
                <h1 class="mt-4 text-2xl font-semibold tracking-tight">{{ $task->title }}</h1>

                <div class="mt-5 border-t border-line pt-5">
                    <h2 class="mb-2 text-xs font-semibold uppercase tracking-wider text-ink-subtle">Descrição</h2>
                    @if ($task->description)
                        <p class="whitespace-pre-line text-sm leading-relaxed text-ink">{{ $task->description }}</p>
                    @else
                        <p class="text-sm italic text-ink-subtle">Nenhuma descrição informada.</p>
                    @endif
                </div>
            </x-card>

            <x-card id="comentarios">
                <x-slot name="title">Comentários ({{ $task->comments->count() }})</x-slot>

                @if ($task->comments->isEmpty())
                    <x-empty-state compact icon="chat" title="Nenhum comentário ainda" description="Use os comentários para registrar dúvidas, decisões e andamento." />
                @else
                    <ol class="space-y-5">
                        @foreach ($task->comments as $comment)
                            <li class="flex gap-3">
                                <x-avatar :user="$comment->author" size="md" />
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-baseline gap-x-2">
                                        <span class="text-sm font-semibold text-ink">{{ $comment->author->name }}</span>
                                        <time class="text-xs text-ink-subtle" datetime="{{ $comment->created_at->toIso8601String() }}" title="{{ $comment->created_at->format('d/m/Y H:i') }}">{{ $comment->created_at->diffForHumans() }}</time>
                                    </div>
                                    <div class="mt-1.5 rounded-xl rounded-tl-sm bg-surface-muted px-3.5 py-2.5 text-sm leading-relaxed text-ink">
                                        <p class="whitespace-pre-line">{{ $comment->body }}</p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif

                @if ($canEdit)
                    <form method="POST" action="{{ route('tasks.comments.store', $task) }}" class="mt-6 flex gap-3 border-t border-line pt-5">
                        @csrf
                        <x-avatar :user="auth()->user()" size="md" />
                        <div class="flex-1">
                            <x-textarea name="body" rows="3" placeholder="Escreva um comentário..." aria-label="Comentário">{{ old('body') }}</x-textarea>
                            <x-input-error :messages="$errors->get('body')" class="mt-1.5" />
                            <div class="mt-3 flex justify-end">
                                <x-button size="sm" icon="send">Comentar</x-button>
                            </div>
                        </div>
                    </form>
                @endif
            </x-card>
        </div>

        <aside class="space-y-6">
            <x-card>
                <dl class="space-y-5 text-sm">
                    <div>
                        <dt class="mb-1.5 text-xs font-semibold uppercase tracking-wider text-ink-subtle">Status</dt>
                        <dd>
                            @if ($canEdit)
                                <form method="POST" action="{{ route('tasks.status', $task) }}">
                                    @csrf @method('PATCH')
                                    <x-select name="status" :options="\App\Enums\TaskStatus::options()" :selected="$task->status->value" onchange="this.form.submit()" aria-label="Alterar status" />
                                    <noscript><x-button size="sm" class="mt-2">Salvar</x-button></noscript>
                                </form>
                            @else
                                <x-badge :color="$task->status->color()" dot>{{ $task->status->label() }}</x-badge>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="mb-1.5 text-xs font-semibold uppercase tracking-wider text-ink-subtle">Responsável</dt>
                        <dd class="flex items-center gap-2">
                            <x-avatar :user="$task->assignee" size="sm" />
                            <span class="{{ $task->assignee ? 'text-ink' : 'text-ink-subtle' }}">{{ $task->assignee?->name ?? 'Sem responsável' }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="mb-1.5 text-xs font-semibold uppercase tracking-wider text-ink-subtle">Prazo</dt>
                        <dd>
                            @if ($task->due_date)
                                <x-due-date :date="$task->due_date" :overdue="$task->isOverdue()" :soon="$task->isDueSoon()" class="!text-sm" />
                                <span class="ml-1 text-xs text-ink-subtle">{{ $task->due_date->format('d/m/Y') }}</span>
                            @else
                                <span class="text-ink-subtle">Sem prazo</span>
                            @endif
                        </dd>
                    </div>
                    <div class="border-t border-line pt-5 text-xs text-ink-muted">
                        Criada por <span class="font-medium text-ink">{{ $task->creator?->name ?? 'usuário removido' }}</span>
                        {{ $task->created_at->diffForHumans() }}.<br>
                        Atualizada {{ $task->updated_at->diffForHumans() }}.
                    </div>
                </dl>
            </x-card>

            @if ($canEdit)
                <div class="flex gap-2">
                    <x-button variant="secondary" icon="pencil" :href="route('projects.tasks.edit', [$project, $task])" class="flex-1">Editar</x-button>
                    @can('delete', $task)
                        <x-button variant="secondary" type="button" icon="trash" class="text-rose-600 dark:text-rose-400" x-data @click="$dispatch('open-modal', 'delete-task')">Excluir</x-button>
                    @endcan
                </div>
            @endif
        </aside>
    </div>

    @can('delete', $task)
        @push('modals')
            <x-modal name="delete-task" maxWidth="md">
                <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $task]) }}" class="p-6">
                    @csrf @method('DELETE')
                    <h2 class="text-base font-semibold">Excluir esta tarefa?</h2>
                    <p class="mt-1 text-sm text-ink-muted">“{{ $task->title }}” e seus comentários serão removidos permanentemente.</p>
                    <div class="mt-6 flex justify-end gap-2">
                        <x-button variant="secondary" type="button" x-on:click="$dispatch('close-modal', 'delete-task')">Cancelar</x-button>
                        <x-button variant="danger">Excluir tarefa</x-button>
                    </div>
                </form>
            </x-modal>
        @endpush
    @endcan
</x-app-layout>
