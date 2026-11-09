<section>
    <header class="mb-5">
        <h2 class="text-base font-semibold text-ink">Excluir conta</h2>
        <p class="mt-1 text-sm text-ink-muted">Ao excluir a conta, seus dados e comentários são removidos permanentemente. Projetos dos quais você é proprietário precisam ser excluídos antes.</p>
    </header>

    <x-button variant="danger" type="button" icon="trash" x-data x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">Excluir conta</x-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" maxWidth="md">
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-base font-semibold text-ink">Tem certeza de que deseja excluir sua conta?</h2>
            <p class="mt-1 text-sm text-ink-muted">Esta ação não pode ser desfeita. Digite sua senha para confirmar.</p>

            <x-field label="Senha" for="password" error="password" bag="userDeletion" class="mt-5">
                <x-text-input id="password" name="password" type="password" placeholder="Sua senha" />
            </x-field>

            <div class="mt-6 flex justify-end gap-2">
                <x-button variant="secondary" type="button" x-on:click="$dispatch('close-modal', 'confirm-user-deletion')">Cancelar</x-button>
                <x-button variant="danger">Excluir conta</x-button>
            </div>
        </form>
    </x-modal>
</section>
