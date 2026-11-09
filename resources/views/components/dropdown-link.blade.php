@props(['icon' => null, 'danger' => false, 'as' => 'a'])

@php
    $classes = 'flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm transition '
        .($danger ? 'text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-500/10' : 'text-ink-muted hover:bg-surface-muted hover:text-ink');
@endphp

@if ($as === 'button')
    <button type="submit" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" class="size-4" />@endif
        {{ $slot }}
    </button>
@else
    <a {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" class="size-4" />@endif
        {{ $slot }}
    </a>
@endif
