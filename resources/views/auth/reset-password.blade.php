<x-guest-layout>
    <x-slot name="title">Nova senha</x-slot>

    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight">Defina uma nova senha</h1>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-field label="E-mail" for="email">
            <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
        </x-field>
        <x-field label="Nova senha" for="password">
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
        </x-field>
        <x-field label="Confirmar senha" for="password_confirmation">
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
        </x-field>

        <x-button class="w-full">Redefinir senha</x-button>
    </form>
</x-guest-layout>
