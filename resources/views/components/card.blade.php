{{-- Cartão de conteúdo. Slots opcionais: `title`, `actions` e `footer`. --}}
@props(['padding' => true])

<section {{ $attributes->merge(['class' => 'rounded-2xl border border-line bg-surface shadow-card']) }}>
    @isset($title)
        <header class="flex items-center justify-between gap-4 border-b border-line px-5 py-4 sm:px-6">
            <h2 class="text-sm font-semibold text-ink">{{ $title }}</h2>
            @isset($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endisset
        </header>
    @endisset

    <div @class(['p-5 sm:p-6' => $padding])>
        {{ $slot }}
    </div>

    @isset($footer)
        <footer class="border-t border-line px-5 py-3 sm:px-6">{{ $footer }}</footer>
    @endisset
</section>
