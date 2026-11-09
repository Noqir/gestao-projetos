<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        @include('layouts.partials.head', ['title' => $title ?? 'Erro'])
    </head>
    <body class="min-h-screen font-sans antialiased">
        <main class="flex min-h-screen flex-col items-center justify-center px-6 text-center">
            <x-application-logo class="mb-8 size-12" />
            <p class="text-sm font-semibold text-brand-600 dark:text-brand-400">Erro {{ $code }}</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">{{ $heading }}</h1>
            <p class="mt-3 max-w-md text-base text-ink-muted">{{ $message }}</p>
            <div class="mt-8 flex gap-3">
                <x-button variant="secondary" type="button" icon="arrow-left" onclick="history.back()">Voltar</x-button>
                <x-button :href="url('/dashboard')" icon="dashboard">Ir para o painel</x-button>
            </div>
        </main>
    </body>
</html>
