{{--
    Janela modal. Abrir: $dispatch('open-modal', 'nome'); fechar: $dispatch('close-modal', 'nome').
    Slots opcionais: `title` e `footer`.
--}}
@props(['name', 'show' => false, 'maxWidth' => 'lg'])

@php
    $maxWidth = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
    ][$maxWidth];
@endphp

<div
    x-data="{ show: @js($show) }"
    x-init="$watch('show', value => {
        document.body.classList.toggle('overflow-y-hidden', value);
        if (value) setTimeout(() => $el.querySelector('[autofocus], input:not([type=hidden]), textarea, select')?.focus(), 120);
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 sm:py-16"
    style="display: {{ $show ? 'block' : 'none' }};"
    role="dialog" aria-modal="true"
>
    <div x-show="show" x-on:click="show = false"
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-[2px]"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

    <div x-show="show"
        class="relative mx-auto w-full overflow-hidden rounded-2xl border border-line bg-surface-raised shadow-lift {{ $maxWidth }}"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 sm:scale-100" x-transition:leave-end="opacity-0 sm:scale-95">
        @isset($title)
            <div class="flex items-center justify-between border-b border-line px-6 py-4">
                <h2 class="text-base font-semibold text-ink">{{ $title }}</h2>
                <button type="button" x-on:click="show = false" class="rounded-md p-1 text-ink-subtle transition hover:bg-surface-muted hover:text-ink" aria-label="Fechar">
                    <x-icon name="x-mark" class="size-5" />
                </button>
            </div>
        @endisset
        {{ $slot }}
    </div>
</div>
