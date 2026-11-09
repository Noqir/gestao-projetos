<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        @include('layouts.partials.head')
    </head>
    <body class="min-h-screen font-sans antialiased" x-data="{ sidebar: false }">
        {{-- Barra lateral fixa (desktop) --}}
        <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 border-r border-line bg-surface lg:block">
            @include('layouts.navigation')
        </aside>

        {{-- Gaveta de navegação (mobile) --}}
        <div x-show="sidebar" x-cloak class="fixed inset-0 z-40 lg:hidden" role="dialog" aria-modal="true">
            <div x-show="sidebar" x-transition.opacity @click="sidebar = false" class="fixed inset-0 bg-slate-900/50"></div>
            <aside x-show="sidebar"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                class="fixed inset-y-0 left-0 w-72 max-w-[85vw] border-r border-line bg-surface">
                @include('layouts.navigation')
            </aside>
        </div>

        <div class="lg:pl-64">
            {{-- Barra superior (mobile) --}}
            <header class="sticky top-0 z-20 flex h-14 items-center justify-between border-b border-line bg-surface/85 px-4 backdrop-blur lg:hidden">
                <button type="button" @click="sidebar = true" class="-ml-1 rounded-lg p-2 text-ink-muted hover:bg-surface-muted" aria-label="Abrir menu">
                    <x-icon name="menu" class="size-5" />
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-semibold">
                    <x-application-logo class="size-7" />
                    <span>Gestão de Projetos</span>
                </a>
                <x-avatar :user="Auth::user()" size="sm" />
            </header>

            <main class="mx-auto w-full max-w-[90rem] px-4 py-8 sm:px-6 lg:px-10 lg:py-10 animate-fade-in">
                {{ $slot }}
            </main>
        </div>

        <x-flash />
        @stack('modals')
    </body>
</html>
