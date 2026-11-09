<x-app-layout>
    <x-slot name="title">Painel</x-slot>

    @php
        $user = auth()->user();
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Bom dia' : ($hour < 18 ? 'Boa tarde' : 'Boa noite');
    @endphp

    <x-page-header :title="$greeting.', '.$user->firstName()" :subtitle="ucfirst(now()->translatedFormat('l, d \d\e F')).' · visão geral dos seus projetos ativos'">
        <x-button :href="route('projects.create')" icon="plus">Novo projeto</x-button>
    </x-page-header>

    @unless ($hasProjects)
        <x-card>
            <x-empty-state icon="chart" title="Seu painel ainda está vazio" description="Crie um projeto ou peça para ser adicionado a um. Os indicadores aparecem aqui assim que houver tarefas.">
                <x-button :href="route('projects.create')" icon="plus">Criar meu primeiro projeto</x-button>
            </x-empty-state>
        </x-card>
    @else
        {{-- Indicadores principais --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['Total de tarefas', $total, 'em projetos ativos', 'board', 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300'],
                ['Em andamento', $byStatus['doing'] + $byStatus['review'], $byStatus['review'].' em revisão', 'bolt', 'bg-sky-50 text-sky-600 dark:bg-sky-500/15 dark:text-sky-300'],
                ['Concluídas', $byStatus['done'], $completion.'% do total', 'check-circle', 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300'],
                ['Atrasadas', $overdue, $overdue ? 'precisam de atenção' : 'tudo em dia', 'warning', $overdue ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/15 dark:text-rose-300' : 'bg-surface-muted text-ink-subtle'],
            ] as [$label, $value, $hint, $icon, $tone])
                <div class="rounded-2xl border border-line bg-surface p-5 shadow-card" data-stat="{{ $label }}">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-ink-muted">{{ $label }}</p>
                        <span class="flex size-9 items-center justify-center rounded-xl {{ $tone }}"><x-icon :name="$icon" class="size-5" /></span>
                    </div>
                    <p class="mt-3 text-3xl font-semibold tabular-nums tracking-tight">{{ $value }}</p>
                    <p class="mt-1 text-xs text-ink-subtle">{{ $hint }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                {{-- Tarefas por status (GROUP BY status) --}}
                <x-card>
                    <x-slot name="title">Tarefas por status</x-slot>
                    <div class="flex h-3 overflow-hidden rounded-full bg-surface-muted">
                        @foreach ($statuses as $status)
                            @if ($byStatus[$status->value] > 0)
                                <div class="{{ $status->barClass() }} h-full transition-all duration-700 first:rounded-l-full last:rounded-r-full"
                                    style="width: {{ $byStatus[$status->value] * 100 / max($total, 1) }}%" title="{{ $status->label() }}: {{ $byStatus[$status->value] }}"></div>
                            @endif
                        @endforeach
                    </div>
                    <dl class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-4">
                        @foreach ($statuses as $status)
                            <div>
                                <dt class="flex items-center gap-1.5 text-xs text-ink-muted"><span class="size-2 rounded-full {{ $status->barClass() }}"></span>{{ $status->label() }}</dt>
                                <dd class="mt-1 text-xl font-semibold tabular-nums">
                                    {{ $byStatus[$status->value] }}
                                    <span class="text-xs font-normal text-ink-subtle">{{ $total ? round($byStatus[$status->value] * 100 / $total) : 0 }}%</span>
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </x-card>

                <div class="grid gap-6 lg:grid-cols-2">
                    {{-- Carga por responsável (JOIN users + GROUP BY) --}}
                    <x-card>
                        <x-slot name="title">Carga por responsável</x-slot>
                        @forelse ($workload as $person)
                            @php $max = max($workload->max('open_tasks'), 1); @endphp
                            <div class="flex items-center gap-3 py-2">
                                <x-avatar :user="$person" size="sm" />
                                <div class="min-w-0 flex-1">
                                    <div class="mb-1 flex items-center justify-between text-xs">
                                        <span class="truncate font-medium text-ink">{{ $person->name }}</span>
                                        <span class="tabular-nums text-ink-muted"><span class="font-semibold text-ink">{{ $person->open_tasks }}</span> abertas · {{ $person->done_tasks }} feitas</span>
                                    </div>
                                    <div class="h-1.5 overflow-hidden rounded-full bg-surface-muted">
                                        <div class="h-full rounded-full bg-brand-500" style="width: {{ $person->open_tasks * 100 / $max }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <x-empty-state compact icon="users" title="Nenhuma tarefa atribuída" description="Defina responsáveis nas tarefas para ver a distribuição." />
                        @endforelse
                    </x-card>

                    {{-- Progresso dos projetos --}}
                    <x-card>
                        <x-slot name="title">Progresso dos projetos</x-slot>
                        <x-slot name="actions"><a href="{{ route('projects.index') }}" class="text-xs font-medium text-brand-600 hover:text-brand-500 dark:text-brand-300">Ver todos</a></x-slot>
                        <ul class="space-y-4">
                            @foreach ($projects as $project)
                                <li>
                                    <a href="{{ route('projects.show', $project) }}" class="group block">
                                        <div class="mb-1.5 flex items-center justify-between gap-3 text-sm">
                                            <span class="flex min-w-0 items-center gap-2">
                                                <span class="size-2 shrink-0 rounded-full {{ $project->colorClass() }}"></span>
                                                <span class="truncate font-medium text-ink group-hover:text-brand-600 dark:group-hover:text-brand-300">{{ $project->name }}</span>
                                            </span>
                                            <span class="shrink-0 text-xs tabular-nums text-ink-muted">{{ $project->done_tasks_count }}/{{ $project->tasks_count }}</span>
                                        </div>
                                        <div class="h-1.5 overflow-hidden rounded-full bg-surface-muted">
                                            <div class="h-full rounded-full bg-emerald-500" style="width: {{ $project->progress() }}%"></div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </x-card>
                </div>
            </div>

            <div class="space-y-6">
                <x-card :padding="false">
                    <x-slot name="title">Minhas tarefas abertas</x-slot>
                    @forelse ($myTasks as $task)
                        <a href="{{ route('projects.tasks.show', [$task->project_id, $task]) }}" class="flex items-start gap-3 border-b border-line px-5 py-3.5 transition last:border-0 hover:bg-surface-muted/60 sm:px-6">
                            <span class="mt-1.5 size-2 shrink-0 rounded-full {{ $task->status->barClass() }}" title="{{ $task->status->label() }}"></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-ink">{{ $task->title }}</p>
                                <p class="truncate text-xs text-ink-subtle">{{ $task->project->name }} · {{ $task->status->label() }}</p>
                            </div>
                            <x-due-date :date="$task->due_date" :overdue="$task->isOverdue()" :soon="$task->isDueSoon()" class="shrink-0" />
                        </a>
                    @empty
                        <x-empty-state compact icon="check-circle" title="Nada pendente com você" description="Tarefas atribuídas a você aparecem aqui." />
                    @endforelse
                </x-card>

                <x-card>
                    <x-slot name="title">Atividade recente</x-slot>
                    @if ($activity->isEmpty())
                        <x-empty-state compact icon="clock" title="Sem atividade ainda" />
                    @else
                        <ol>
                            @foreach ($activity as $item)
                                <x-activity-item :activity="$item" show-project :last="$loop->last" />
                            @endforeach
                        </ol>
                    @endif
                </x-card>
            </div>
        </div>
    @endunless
</x-app-layout>
