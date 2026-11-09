{{-- Cabeçalho comum às páginas do projeto: título, ações do proprietário e abas. --}}
@props(['project', 'tab'])

<div class="mb-6">
    <div class="mb-3 flex items-center gap-1.5 text-sm text-ink-muted">
        <a href="{{ route('projects.index') }}" class="transition hover:text-ink">Projetos</a>
        <x-icon name="chevron-right" class="size-3.5 text-ink-subtle" />
        <span class="truncate text-ink">{{ $project->name }}</span>
    </div>

    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="flex min-w-0 items-start gap-4">
            <span class="mt-1 flex size-11 shrink-0 items-center justify-center rounded-xl text-lg font-semibold text-white shadow-sm {{ $project->colorClass() }}">
                {{ mb_strtoupper(mb_substr($project->name, 0, 1)) }}
            </span>
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="truncate text-2xl font-semibold tracking-tight">{{ $project->name }}</h1>
                    @if ($project->archived)
                        <x-badge color="gray"><x-icon name="archive" class="size-3.5" />Arquivado</x-badge>
                    @endif
                </div>
                @if ($project->description)
                    <p class="mt-1 max-w-2xl text-sm text-ink-muted">{{ $project->description }}</p>
                @endif
                <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-ink-muted">
                    <x-avatar-stack :users="$project->members" />
                    @if ($project->due_date)
                        <span class="inline-flex items-center gap-1.5 {{ $project->isOverdue() ? 'text-rose-600 dark:text-rose-400' : '' }}">
                            <x-icon name="flag" class="size-4" />Entrega em {{ $project->due_date->translatedFormat('d \d\e F \d\e Y') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            @if ($tab === 'board' && auth()->user()->can('createTask', $project))
                <x-button icon="plus" type="button" x-data @click="$dispatch('open-modal', 'create-task')">Nova tarefa</x-button>
            @endif

            @can('update', $project)
                <x-dropdown width="w-52">
                    <x-slot name="trigger">
                        <x-button variant="secondary" type="button" class="px-2.5" aria-label="Mais ações">
                            <x-icon name="dots" class="size-5" />
                        </x-button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('projects.edit', $project)" icon="pencil">Editar projeto</x-dropdown-link>
                        @if ($project->archived)
                            <form method="POST" action="{{ route('projects.restore', $project) }}">
                                @csrf @method('PATCH')
                                <x-dropdown-link as="button" icon="unarchive">Reativar projeto</x-dropdown-link>
                            </form>
                        @else
                            <form method="POST" action="{{ route('projects.archive', $project) }}">
                                @csrf @method('PATCH')
                                <x-dropdown-link as="button" icon="archive">Arquivar projeto</x-dropdown-link>
                            </form>
                        @endif
                        <div class="my-1 border-t border-line"></div>
                        <x-dropdown-link as="button" type="button" icon="trash" danger @click="$dispatch('open-modal', 'delete-project')">Excluir projeto</x-dropdown-link>
                    </x-slot>
                </x-dropdown>
            @endcan
        </div>
    </div>

    @if ($project->archived)
        <div class="mt-5 flex items-center gap-2 rounded-xl border border-line bg-surface-muted px-4 py-3 text-sm text-ink-muted">
            <x-icon name="lock" class="size-4 shrink-0" />
            Este projeto está arquivado e disponível apenas para consulta.
        </div>
    @endif

    <nav class="mt-6 flex gap-1 overflow-x-auto border-b border-line" aria-label="Seções do projeto">
        @foreach ([
            'board' => ['Quadro', 'board', route('projects.show', $project)],
            'members' => ['Membros', 'users', route('projects.members.index', $project)],
            'activity' => ['Atividades', 'clock', route('projects.activity', $project)],
        ] as $key => [$label, $icon, $url])
            <a href="{{ $url }}" @class([
                '-mb-px inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-3 pb-3 pt-1 text-sm font-medium transition',
                'border-brand-600 text-ink dark:border-brand-400' => $tab === $key,
                'border-transparent text-ink-muted hover:border-line-strong hover:text-ink' => $tab !== $key,
            ])>
                <x-icon :name="$icon" class="size-4" />
                {{ $label }}
                @if ($key === 'members')
                    <span class="rounded-full bg-surface-muted px-1.5 text-[11px] text-ink-muted">{{ $project->members->count() }}</span>
                @endif
            </a>
        @endforeach
    </nav>
</div>

@can('delete', $project)
    @push('modals')
        <x-modal name="delete-project" maxWidth="md">
            <form method="POST" action="{{ route('projects.destroy', $project) }}" class="p-6">
                @csrf @method('DELETE')
                <div class="flex gap-4">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-600 dark:bg-rose-500/15 dark:text-rose-400">
                        <x-icon name="warning" class="size-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold">Excluir “{{ $project->name }}”?</h2>
                        <p class="mt-1 text-sm text-ink-muted">Todas as tarefas, comentários e o histórico serão apagados permanentemente. Se quiser apenas tirar o projeto da lista, prefira arquivá-lo.</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <x-button variant="secondary" type="button" x-on:click="$dispatch('close-modal', 'delete-project')">Cancelar</x-button>
                    <x-button variant="danger">Excluir projeto</x-button>
                </div>
            </form>
        </x-modal>
    @endpush
@endcan
