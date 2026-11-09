<x-guest-layout>
    <x-slot name="title">Recuperar senha</x-slot>

    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight">Esqueceu a senha?</h1>
        <p class="mt-1.5 text-sm text-ink-muted">Informe seu e-mail e enviaremos um link para você criar uma nova senha.</p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <x-field label="E-mail" for="email">
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
        </x-field>
        <x-button class="w-full">Enviar link de redefinição</x-button>
    </form>

    <p class="mt-8 text-center text-sm">
        <a href="{{ route('login') }}" class="font-medium text-brand-600 hover:text-brand-500 dark:text-brand-300">Voltar para o login</a>
    </p>
</x-guest-layout>
