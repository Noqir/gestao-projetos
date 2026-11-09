<x-guest-layout>
    <x-slot name="title">Confirmar senha</x-slot>

    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight">Confirme sua senha</h1>
        <p class="mt-1.5 text-sm text-ink-muted">Esta é uma área protegida. Confirme sua senha para continuar.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf
        <x-field label="Senha" for="password">
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
        </x-field>
        <x-button class="w-full">Confirmar</x-button>
    </form>
</x-guest-layout>
