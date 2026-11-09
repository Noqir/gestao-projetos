@props(['align' => 'right', 'width' => 'w-56', 'direction' => 'down'])

@php
    $alignment = $align === 'left' ? 'left-0 origin-top-left' : 'right-0 origin-top-right';
    $position = $direction === 'up' ? 'bottom-full mb-2' : 'mt-2';
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
    <div @click="open = ! open">{{ $trigger }}</div>

    <div x-show="open" x-cloak
        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 {{ $position }} {{ $width }} {{ $alignment }} rounded-xl border border-line bg-surface-raised p-1 shadow-lift"
        @click="open = false">
        {{ $content }}
    </div>
</div>
