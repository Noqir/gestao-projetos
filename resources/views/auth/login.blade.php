<x-guest-layout>
    <x-slot name="title">Entrar</x-slot>

    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight">Bem-vindo de volta</h1>
        <p class="mt-1.5 text-sm text-ink-muted">Entre com seu e-mail e senha para acessar seus projetos.</p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-field label="E-mail" for="email">
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="voce@empresa.com" />
        </x-field>

        <x-field label="Senha" for="password">
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
        </x-field>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-ink-muted">
                <input id="remember_me" type="checkbox" class="form-checkbox" name="remember">
                Manter conectado
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-brand-600 hover:text-brand-500 dark:text-brand-300" href="{{ route('password.request') }}">Esqueceu a senha?</a>
            @endif
        </div>

        <x-button class="w-full">Entrar</x-button>
    </form>

    <p class="mt-8 text-center text-sm text-ink-muted">
        Ainda não tem conta?
        <a href="{{ route('register') }}" class="font-medium text-brand-600 hover:text-brand-500 dark:text-brand-300">Criar conta</a>
    </p>

    @if (app()->isLocal())
        <div class="mt-8 rounded-xl border border-dashed border-line-strong bg-surface p-4 text-xs text-ink-muted">
            <p class="mb-1 flex items-center gap-1.5 font-semibold text-ink"><x-icon name="info" class="size-4 text-brand-500" />Acesso de demonstração</p>
            <p><span class="font-mono">admin@demo.test</span> ou <span class="font-mono">membro@demo.test</span> · senha <span class="font-mono">password</span></p>
        </div>
    @endif
</x-guest-layout>
