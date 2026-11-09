{{-- Avisos (toasts): mensagens de sessão e eventos `notify` disparados pelo JavaScript. --}}
@php
    $initial = collect([
        session('success') ? ['message' => session('success'), 'type' => 'success'] : null,
        session('error') ? ['message' => session('error'), 'type' => 'error'] : null,
        session('status') && ! in_array(session('status'), ['profile-updated', 'password-updated', 'verification-link-sent'])
            ? ['message' => session('status'), 'type' => 'success'] : null,
    ])->filter()->values();
@endphp

<div x-data="toaster(@js($initial))" aria-live="polite"
    class="pointer-events-none fixed inset-x-0 bottom-0 z-[60] flex flex-col items-center gap-2 p-4 sm:bottom-auto sm:left-auto sm:right-0 sm:top-0 sm:items-end sm:p-6">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-4"
            x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl border border-line bg-surface-raised p-4 shadow-lift">
            <div class="mt-0.5 shrink-0" :class="toast.type === 'error' ? 'text-rose-500' : 'text-emerald-500'">
                <template x-if="toast.type === 'error'"><x-icon name="x-circle" class="size-5" /></template>
                <template x-if="toast.type !== 'error'"><x-icon name="check-circle" class="size-5" /></template>
            </div>
            <p class="flex-1 text-sm font-medium text-ink" x-text="toast.message"></p>
            <button type="button" @click="dismiss(toast.id)" class="shrink-0 rounded text-ink-subtle transition hover:text-ink" aria-label="Fechar aviso">
                <x-icon name="x-mark" class="size-4" />
            </button>
        </div>
    </template>
</div>
