<x-app-layout>
    <x-slot name="title">Atividades · {{ $project->name }}</x-slot>

    <x-project-header :project="$project" tab="activity" />

    <div class="mx-auto max-w-3xl">
        @if ($activities->isEmpty())
            <x-card><x-empty-state icon="clock" title="Nenhuma atividade registrada" description="Criações, movimentações e comentários aparecem aqui automaticamente." /></x-card>
        @else
            @foreach ($activities->groupBy(fn ($a) => $a->created_at->toDateString()) as $day => $items)
                @php $date = \Illuminate\Support\Carbon::parse($day); @endphp
                <h2 class="mb-3 mt-8 text-xs font-semibold uppercase tracking-wider text-ink-subtle first:mt-0">
                    {{ $date->isToday() ? 'Hoje' : ($date->isYesterday() ? 'Ontem' : $date->translatedFormat('l, d \d\e F')) }}
                </h2>
                <x-card>
                    <ol>
                        @foreach ($items as $activity)
                            <x-activity-item :activity="$activity" :last="$loop->last" />
                        @endforeach
                    </ol>
                </x-card>
            @endforeach

            <div class="mt-6"><x-pagination :paginator="$activities" /></div>
        @endif
    </div>
</x-app-layout>
