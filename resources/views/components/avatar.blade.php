{{-- Avatar com iniciais e cor estável derivada do id do usuário. --}}
@props(['user' => null, 'size' => 'md'])

@php
    $palette = [
        'bg-brand-500', 'bg-sky-500', 'bg-emerald-500', 'bg-amber-500',
        'bg-rose-500', 'bg-violet-500', 'bg-teal-500', 'bg-orange-500',
    ];
    $sizes = [
        'xs' => 'size-6 text-[10px]',
        'sm' => 'size-7 text-[11px]',
        'md' => 'size-9 text-xs',
        'lg' => 'size-11 text-sm',
    ];
    $name = $user?->name ?? '?';
    $initials = collect(preg_split('/\s+/', trim($name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    $color = $user ? $palette[$user->id % count($palette)] : 'bg-slate-300 dark:bg-slate-600';
@endphp

<span title="{{ $user?->name ?? 'Sem responsável' }}"
    {{ $attributes->merge(['class' => 'inline-flex shrink-0 select-none items-center justify-center rounded-full font-semibold text-white ring-2 ring-surface '.$sizes[$size].' '.$color]) }}>
    @if ($user)
        {{ $initials }}
    @else
        <x-icon name="user" class="size-4" />
    @endif
</span>
