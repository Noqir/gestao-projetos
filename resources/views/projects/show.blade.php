<x-app-layout>
    <x-slot name="title">{{ $project->name }}</x-slot>

    <x-project-header :project="$project" tab="board" />

    @php
        $canMove = auth()->user()->can('createTask', $project);
        $hasFilters = collect($filters)->filter()->isNotEmpty();
        $counts = collect($columns)->map->count()->all();
    @endphp

    {{-- Filtros (RF09): processados no servidor pelo escopo Task::filter --}}
    <form method="GET" action="{{ route('projects.show', $project) }}" class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1 sm:max-w-xs">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-ink-subtle" />
            <x-text-input name="q" :value="$filters['q'] ?? ''" placeholder="Buscar tarefas..." class="pl-9" aria-label="Buscar tarefas" />
        </div>
        <x-select name="assignee" aria-label="Filtrar por responsável" class="sm:w-56" onchange="this.form.submit()"
            :options="['none' => 'Sem responsável'] + $project->members->pluck('name', 'id')->all()"
            :selected="$filters['assignee'] ?? ''" placeholder="Todos os responsáveis" />
        <x-select name="priority" aria-label="Filtrar por prioridade" class="sm:w-52" onchange="this.form.submit()"
            :options="$priorities" :selected="$filters['priority'] ?? ''" placeholder="Todas as prioridades" />
        <noscript><x-button variant="secondary">Filtrar</x-button></noscript>
        @if ($hasFilters)
            <x-button variant="ghost" :href="route('projects.show', $project)" icon="x-mark">Limpar filtros</x-button>
        @endif
    </form>

    {{-- Quadro Kanban: o componente Alpine "kanban" liga o SortableJS às colunas --}}
    <div x-data="kanban(@js($counts), @js($canMove))" class="scrollbar-thin -mx-4 overflow-x-auto px-4 pb-4 sm:-mx-6 sm:px-6 lg:-mx-10 lg:px-10">
        <div class="grid min-w-[64rem] grid-cols-4 gap-4">
            @foreach ($statuses as $status)
                <section class="flex flex-col rounded-2xl border border-line bg-surface-muted/60 p-3" aria-label="{{ $status->label() }}">
                    <header class="mb-3 flex items-center justify-between px-1">
                        <div class="flex items-center gap-2">
                            <span class="size-2 rounded-full {{ $status->barClass() }}"></span>
                            <h2 class="text-sm font-semibold text-ink">{{ $status->label() }}</h2>
                            <span class="rounded-full bg-surface px-2 py-0.5 text-xs font-medium text-ink-muted ring-1 ring-line" x-text="counts['{{ $status->value }}']">{{ $counts[$status->value] }}</span>
                        </div>
                        @if ($canMove)
                            <button type="button" class="rounded-md p-1 text-ink-subtle transition hover:bg-surface hover:text-ink"
                                title="Nova tarefa em {{ $status->label() }}" aria-label="Nova tarefa em {{ $status->label() }}"
                                @click="$dispatch('task-status', '{{ $status->value }}'); $dispatch('open-modal', 'create-task')">
                                <x-icon name="plus" class="size-4" />
                            </button>
                        @endif
                    </header>

                    <div class="relative flex-1">
                        <div data-column="{{ $status->value }}" class="flex min-h-[10rem] flex-col gap-2.5 pb-1">
                            @foreach ($columns[$status->value] as $task)
                                @include('tasks.partials.card', ['task' => $task, 'project' => $project, 'canMove' => $canMove])
                            @endforeach
                        </div>
                        <div x-show="counts['{{ $status->value }}'] === 0" @if ($counts[$status->value] > 0) x-cloak @endif
                            class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-line-strong/70 px-4 text-center text-xs text-ink-subtle">
                            <x-icon :name="$hasFilters ? 'filter' : 'board'" class="mb-1.5 size-5" />
                            {{ $hasFilters ? 'Nenhuma tarefa com estes filtros' : ($canMove ? 'Arraste tarefas para cá' : 'Nenhuma tarefa') }}
                        </div>
                    </div>
                </section>
            @endforeach
        </div>
    </div>

    @if ($canMove)
        @push('modals')
            @include('tasks.partials.create-modal')
        @endpush
    @endif
</x-app-layout>
