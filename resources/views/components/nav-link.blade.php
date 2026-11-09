{{-- Item da navegação lateral. --}}
@props(['active' => false, 'icon' => null])

<a {{ $attributes->merge(['class' => 'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition '
    .($active
        ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/15 dark:text-brand-200'
        : 'text-ink-muted hover:bg-surface-muted hover:text-ink')]) }}
    @if ($active) aria-current="page" @endif>
    @if ($icon)
        <x-icon :name="$icon" @class(['size-5 shrink-0', 'text-brand-600 dark:text-brand-300' => $active, 'text-ink-subtle group-hover:text-ink-muted' => ! $active]) />
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>
