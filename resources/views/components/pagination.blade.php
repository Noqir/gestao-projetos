@props(['paginator'])

@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-4 text-sm" aria-label="Paginação">
        <p class="text-ink-muted">Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</p>
        <div class="flex gap-2">
            @if ($paginator->onFirstPage())
                <x-button variant="secondary" size="sm" type="button" disabled>Anterior</x-button>
            @else
                <x-button variant="secondary" size="sm" :href="$paginator->previousPageUrl()">Anterior</x-button>
            @endif
            @if ($paginator->hasMorePages())
                <x-button variant="secondary" size="sm" :href="$paginator->nextPageUrl()">Próxima</x-button>
            @else
                <x-button variant="secondary" size="sm" type="button" disabled>Próxima</x-button>
            @endif
        </div>
    </nav>
@endif
