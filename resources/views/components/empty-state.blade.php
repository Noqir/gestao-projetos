{{-- Estado vazio: ícone, título, descrição e ação opcional (slot). --}}
@props(['icon' => 'folder', 'title', 'description' => null, 'compact' => false])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center '.($compact ? 'px-4 py-8' : 'px-6 py-16')]) }}>
    <div class="mb-4 flex size-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 ring-1 ring-brand-500/10 dark:bg-brand-500/10 dark:text-brand-300">
        <x-icon :name="$icon" class="size-6" />
    </div>
    <h3 class="text-sm font-semibold text-ink">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-ink-muted">{{ $description }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-5">{{ $slot }}</div>
    @endif
</div>
