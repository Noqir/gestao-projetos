<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        @include('layouts.partials.head')
    </head>
    <body class="min-h-screen font-sans antialiased">
        <div class="grid min-h-screen lg:grid-cols-2">
            {{-- Painel de apresentação (desktop) --}}
            <div class="relative hidden overflow-hidden bg-brand-950 lg:flex lg:flex-col lg:justify-between lg:p-12">
                <div class="pointer-events-none absolute -right-24 -top-24 size-96 rounded-full bg-brand-600/40 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-32 -left-16 size-96 rounded-full bg-accent-500/20 blur-3xl"></div>

                <div class="relative flex items-center gap-3 text-white">
                    <x-application-logo class="size-10" />
                    <span class="text-lg font-semibold">Gestão de Projetos</span>
                </div>

                <div class="relative max-w-md">
                    <h2 class="text-4xl font-semibold leading-tight tracking-tight text-white">
                        Projetos organizados, equipe <span class="text-accent-400">alinhada</span>.
                    </h2>
                    <p class="mt-4 text-base leading-relaxed text-brand-200">
                        Crie projetos, convide a equipe, mova tarefas no quadro Kanban e acompanhe o progresso em tempo real no painel.
                    </p>

                    <div class="mt-10 grid grid-cols-3 gap-3">
                        @foreach (['A fazer' => 'bg-slate-400', 'Em andamento' => 'bg-sky-400', 'Concluída' => 'bg-emerald-400'] as $label => $dot)
                            <div class="rounded-xl border border-white/10 bg-white/5 p-3 backdrop-blur">
                                <div class="mb-3 flex items-center gap-1.5 text-[11px] font-medium text-brand-100">
                                    <span class="size-1.5 rounded-full {{ $dot }}"></span>{{ $label }}
                                </div>
                                <div class="space-y-1.5">
                                    <div class="h-2 rounded bg-white/20"></div>
                                    <div class="h-2 w-2/3 rounded bg-white/10"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <p class="relative text-xs text-brand-300">Programação de Sistemas Web · Afya · 2026/2</p>
            </div>

            {{-- Formulário --}}
            <div class="flex flex-col items-center justify-center px-6 py-12 sm:px-12">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <x-application-logo class="size-10" />
                    <span class="text-lg font-semibold">Gestão de Projetos</span>
                </div>
                <div class="w-full max-w-sm">
                    {{ $slot }}
                </div>
            </div>
        </div>
        <x-flash />
    </body>
</html>
