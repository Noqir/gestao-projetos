<section>
    <header class="mb-6">
        <h2 class="text-base font-semibold text-ink">Alterar senha</h2>
        <p class="mt-1 text-sm text-ink-muted">Use uma senha longa e única para manter sua conta segura.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <x-field label="Senha atual" for="update_password_current_password" error="current_password" bag="updatePassword">
            <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
        </x-field>
        <div class="grid gap-5 sm:grid-cols-2">
            <x-field label="Nova senha" for="update_password_password" error="password" bag="updatePassword">
                <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" />
            </x-field>
            <x-field label="Confirmar nova senha" for="update_password_password_confirmation" error="password_confirmation" bag="updatePassword">
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
            </x-field>
        </div>

        <div class="flex items-center gap-3">
            <x-button>Salvar</x-button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="flex items-center gap-1 text-sm text-emerald-600 dark:text-emerald-400">
                    <x-icon name="check" class="size-4" />Senha alterada.
                </p>
            @endif
        </div>
    </form>
</section>
