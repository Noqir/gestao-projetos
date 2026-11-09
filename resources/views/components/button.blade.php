{{--
    Botão padrão. Vira <a> quando recebe `href`.
    Variantes: primary, secondary, danger, ghost. Tamanhos: sm, md.
--}}
@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'icon' => null, 'type' => 'submit'])

@php
    $base = 'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg font-medium transition duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 focus-visible:ring-offset-2 focus-visible:ring-offset-canvas disabled:pointer-events-none disabled:opacity-50 active:scale-[.98]';

    $sizes = [
        'sm' => 'h-8 px-3 text-xs',
        'md' => 'h-10 px-4 text-sm',
    ];

    $variants = [
        'primary' => 'bg-brand-600 text-white shadow-sm hover:bg-brand-500',
        'secondary' => 'border border-line-strong bg-surface text-ink shadow-sm hover:bg-surface-muted',
        'danger' => 'bg-rose-600 text-white shadow-sm hover:bg-rose-500',
        'ghost' => 'text-ink-muted hover:bg-surface-muted hover:text-ink',
    ];

    $classes = $base.' '.$sizes[$size].' '.$variants[$variant];
    $iconSize = $size === 'sm' ? 'size-4' : 'size-[1.125rem]';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" :class="$iconSize" />@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" :class="$iconSize" />@endif
        {{ $slot }}
    </button>
@endif
