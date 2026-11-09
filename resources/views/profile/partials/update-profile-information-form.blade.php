<section>
    <header class="mb-6">
        <h2 class="text-base font-semibold text-ink">Informações do perfil</h2>
        <p class="mt-1 text-sm text-ink-muted">Atualize seu nome e endereço de e-mail.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div class="grid gap-5 sm:grid-cols-2">
            <x-field label="Nome" for="name">
                <x-text-input id="name" name="name" :value="old('name', $user->name)" required autocomplete="name" />
            </x-field>
            <x-field label="E-mail" for="email">
                <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            </x-field>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <p class="text-sm text-ink-muted">
                Seu e-mail ainda não foi verificado.
                <button form="send-verification" class="font-medium text-brand-600 hover:text-brand-500">Reenviar e-mail de verificação.</button>
            </p>
        @endif

        <div class="flex items-center gap-3">
            <x-button>Salvar</x-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="flex items-center gap-1 text-sm text-emerald-600 dark:text-emerald-400">
                    <x-icon name="check" class="size-4" />Perfil atualizado.
                </p>
            @endif
        </div>
    </form>
</section>
