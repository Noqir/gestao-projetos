<x-guest-layout>
    <x-slot name="title">Criar conta</x-slot>

    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight">Crie sua conta</h1>
        <p class="mt-1.5 text-sm text-ink-muted">Leva menos de um minuto. Depois é só criar seu primeiro projeto.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <x-field label="Nome" for="name">
            <x-text-input id="name" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Seu nome completo" />
        </x-field>

        <x-field label="E-mail" for="email">
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="voce@empresa.com" />
        </x-field>

        <x-field label="Senha" for="password" hint="Mínimo de 8 caracteres.">
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
        </x-field>

        <x-field label="Confirmar senha" for="password_confirmation">
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
        </x-field>

        <x-button class="w-full">Criar conta</x-button>
    </form>

    <p class="mt-8 text-center text-sm text-ink-muted">
        Já tem conta?
        <a href="{{ route('login') }}" class="font-medium text-brand-600 hover:text-brand-500 dark:text-brand-300">Entrar</a>
    </p>
</x-guest-layout>
