{{-- Etiqueta colorida (status, prioridade, papel). Cores: gray, brand, sky, amber, emerald, rose. --}}
@props(['color' => 'gray', 'dot' => false])

@php
    $colors = [
        'gray' => 'bg-slate-100 text-slate-700 ring-slate-500/15 dark:bg-slate-500/15 dark:text-slate-300',
        'brand' => 'bg-brand-50 text-brand-700 ring-brand-500/20 dark:bg-brand-500/15 dark:text-brand-300',
        'sky' => 'bg-sky-50 text-sky-700 ring-sky-500/20 dark:bg-sky-500/15 dark:text-sky-300',
        'amber' => 'bg-amber-50 text-amber-800 ring-amber-500/25 dark:bg-amber-500/15 dark:text-amber-300',
        'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-500/20 dark:bg-emerald-500/15 dark:text-emerald-300',
        'rose' => 'bg-rose-50 text-rose-700 ring-rose-500/20 dark:bg-rose-500/15 dark:text-rose-300',
    ];
    $dots = [
        'gray' => 'bg-slate-400', 'brand' => 'bg-brand-500', 'sky' => 'bg-sky-500',
        'amber' => 'bg-amber-500', 'emerald' => 'bg-emerald-500', 'rose' => 'bg-rose-500',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset '.($colors[$color] ?? $colors['gray'])]) }}>
    @if ($dot)<span class="size-1.5 rounded-full {{ $dots[$color] ?? $dots['gray'] }}"></span>@endif
    {{ $slot }}
</span>
