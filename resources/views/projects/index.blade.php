<x-app-layout>
    <x-slot name="title">Projetos</x-slot>

    <x-page-header title="Projetos" subtitle="Todos os projetos dos quais você participa.">
        <x-button :href="route('projects.create')" icon="plus">Novo projeto</x-button>
    </x-page-header>

    <div class="mb-6 inline-flex rounded-xl border border-line bg-surface p-1 text-sm shadow-card">
        @foreach (['active' => ['Ativos', null], 'archived' => ['Arquivados', 'archived']] as $key => [$label, $filter])
            @php $current = ($key === 'archived') === $showingArchived; @endphp
            <a href="{{ route('projects.index', array_filter(['filter' => $filter])) }}" @class([
                'inline-flex items-center gap-2 rounded-lg px-3.5 py-1.5 font-medium transition',
                'bg-brand-600 text-white shadow-sm' => $current,
                'text-ink-muted hover:text-ink' => ! $current,
            ])>
                {{ $label }}
                <span @class(['rounded-full px-1.5 text-[11px]', 'bg-white/20' => $current, 'bg-surface-muted' => ! $current])>{{ $counts[$key] }}</span>
            </a>
        @endforeach
    </div>

    @if ($projects->isEmpty())
        <x-card>
            @if ($showingArchived)
                <x-empty-state icon="archive" title="Nenhum projeto arquivado" description="Projetos concluídos que você arquivar aparecem aqui, disponíveis para consulta." />
            @else
                <x-empty-state icon="folder" title="Você ainda não tem projetos" description="Crie seu primeiro projeto para organizar tarefas no quadro Kanban e convidar sua equipe.">
                    <x-button :href="route('projects.create')" icon="plus">Criar primeiro projeto</x-button>
                </x-empty-state>
            @endif
        </x-card>
    @else
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($projects as $project)
                <a href="{{ route('projects.show', $project) }}"
                    class="group flex flex-col rounded-2xl border border-line bg-surface p-5 shadow-card transition duration-200 hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-lift dark:hover:border-brand-500/40">
                    <div class="flex items-start gap-3">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl font-semibold text-white {{ $project->colorClass() }}">
                            {{ mb_strtoupper(mb_substr($project->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <h2 class="truncate font-semibold text-ink group-hover:text-brand-700 dark:group-hover:text-brand-300">{{ $project->name }}</h2>
                            <p class="text-xs text-ink-subtle">
                                {{ $project->isOwnedBy(auth()->user()) ? 'Você é o proprietário' : 'Você é membro' }}
                            </p>
                        </div>
                        <x-icon name="chevron-right" class="size-4 text-ink-subtle transition group-hover:translate-x-0.5" />
                    </div>

                    <p class="mt-3 line-clamp-2 min-h-[2.5rem] text-sm text-ink-muted">{{ $project->description ?: 'Sem descrição.' }}</p>

                    <div class="mt-5">
                        <div class="mb-1.5 flex items-center justify-between text-xs">
                            <span class="text-ink-muted">{{ $project->done_tasks_count }} de {{ $project->tasks_count }} tarefas concluídas</span>
                            <span class="font-semibold text-ink">{{ $project->progress() }}%</span>
                        </div>
                        <div class="h-1.5 overflow-hidden rounded-full bg-surface-muted">
                            <div class="h-full rounded-full bg-emerald-500 transition-all duration-500" style="width: {{ $project->progress() }}%"></div>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-between border-t border-line pt-4">
                        <x-avatar-stack :users="$project->members" />
                        @if ($project->due_date)
                            <span @class([
                                'inline-flex items-center gap-1 text-xs font-medium',
                                'text-rose-600 dark:text-rose-400' => $project->isOverdue(),
                                'text-ink-muted' => ! $project->isOverdue(),
                            ])>
                                <x-icon name="flag" class="size-3.5" />{{ $project->due_date->translatedFormat('d M Y') }}
                            </span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-app-layout>
