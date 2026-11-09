{{-- Prazo com destaque: vermelho se atrasado, âmbar se vence em até 2 dias. --}}
@props(['date', 'overdue' => false, 'soon' => false])

@if ($date)
    @php
        $label = match (true) {
            $date->isToday() => 'Hoje',
            $date->isTomorrow() => 'Amanhã',
            $date->isYesterday() => 'Ontem',
            default => $date->translatedFormat($date->year === now()->year ? 'd M' : 'd M Y'),
        };
        $tone = $overdue
            ? 'text-rose-600 dark:text-rose-400'
            : ($soon ? 'text-amber-700 dark:text-amber-300' : 'text-ink-muted');
    @endphp
    <span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 text-xs font-medium {$tone}"]) }}
        title="Prazo: {{ $date->format('d/m/Y') }}{{ $overdue ? ' (atrasada)' : '' }}">
        <x-icon :name="$overdue ? 'warning' : 'calendar'" class="size-3.5" />
        {{ $label }}
    </span>
@endif
