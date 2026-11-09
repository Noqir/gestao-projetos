{{-- Cabeçalho de página: título, subtítulo, trilha (slot `breadcrumb`) e ações. --}}
@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="min-w-0">
        @isset($breadcrumb)
            <div class="mb-2 flex items-center gap-1.5 text-sm text-ink-muted">{{ $breadcrumb }}</div>
        @endisset
        <h1 class="truncate text-2xl font-semibold tracking-tight text-ink sm:text-[1.75rem]">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1.5 text-sm text-ink-muted">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $slot }}</div>
    @endif
</div>
