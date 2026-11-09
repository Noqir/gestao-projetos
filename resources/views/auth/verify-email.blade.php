<x-guest-layout>
    <x-slot name="title">Verificar e-mail</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight">Verifique seu e-mail</h1>
        <p class="mt-1.5 text-sm text-ink-muted">Enviamos um link de verificação para o seu e-mail. Não recebeu? Podemos enviar outro.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <x-auth-session-status class="mb-5" status="Um novo link de verificação foi enviado para o seu e-mail." />
    @endif

    <div class="flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-button>Reenviar e-mail</x-button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-button variant="ghost">Sair</x-button>
        </form>
    </div>
</x-guest-layout>
