<x-app-layout>
    <x-slot name="title">Membros · {{ $project->name }}</x-slot>

    <x-project-header :project="$project" tab="members" />

    @php $canManage = auth()->user()->can('manageMembers', $project); @endphp

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]" x-data="{ removeUrl: '', removeName: '' }">
        <x-card :padding="false">
            <x-slot name="title">Equipe do projeto</x-slot>
            <ul class="divide-y divide-line">
                @foreach ($members as $member)
                    @php $role = \App\Enums\ProjectRole::from($member->pivot->role); @endphp
                    <li class="flex items-center gap-4 px-5 py-4 sm:px-6">
                        <x-avatar :user="$member" size="lg" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-ink">
                                {{ $member->name }}
                                @if ($member->is(auth()->user()))<span class="text-xs font-normal text-ink-subtle">(você)</span>@endif
                            </p>
                            <p class="truncate text-xs text-ink-muted">
                                {{ $member->email }}
                                <span class="text-ink-subtle">·</span>
                                {{ trans_choice('{0} nenhuma tarefa aberta|{1} 1 tarefa aberta|[2,*] :count tarefas abertas', $member->open_tasks_count) }}
                            </p>
                        </div>
                        <x-badge class="shrink-0" :color="$role === \App\Enums\ProjectRole::Owner ? 'brand' : 'gray'">{{ $role->label() }}</x-badge>
                        @if ($canManage && $role !== \App\Enums\ProjectRole::Owner)
                            <button type="button" class="shrink-0 rounded-lg p-2 text-ink-subtle transition hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10"
                                title="Remover do projeto" aria-label="Remover {{ $member->name }}"
                                @click="removeUrl = @js(route('projects.members.destroy', [$project, $member])); removeName = @js($member->name); $dispatch('open-modal', 'remove-member')">
                                <x-icon name="user-minus" class="size-5" />
                            </button>
                        @endif
                    </li>
                @endforeach
            </ul>
        </x-card>

        <div>
            @if ($canManage && ! $project->archived)
                <x-card>
                    <x-slot name="title">Adicionar membro</x-slot>
                    <form method="POST" action="{{ route('projects.members.store', $project) }}" class="space-y-4">
                        @csrf
                        <x-field label="E-mail do usuário" for="email" hint="A pessoa precisa já ter uma conta no sistema.">
                            <x-text-input id="email" type="email" name="email" :value="old('email')" placeholder="nome@empresa.com" required />
                        </x-field>
                        <x-button icon="user-plus" class="w-full">Adicionar ao projeto</x-button>
                    </form>
                </x-card>
            @else
                <x-card>
                    <div class="flex gap-3 text-sm text-ink-muted">
                        <x-icon name="info" class="size-5 shrink-0 text-brand-500" />
                        <p>{{ $project->archived ? 'Projetos arquivados não recebem novos membros.' : 'Apenas o proprietário do projeto pode adicionar ou remover membros.' }}</p>
                    </div>
                </x-card>
            @endif
        </div>

        @if ($canManage)
            <x-modal name="remove-member" maxWidth="md">
                <form method="POST" :action="removeUrl" class="p-6">
                    @csrf @method('DELETE')
                    <h2 class="text-base font-semibold">Remover <span x-text="removeName"></span> do projeto?</h2>
                    <p class="mt-1 text-sm text-ink-muted">A pessoa perde o acesso ao projeto e as tarefas sob sua responsabilidade ficam sem responsável.</p>
                    <div class="mt-6 flex justify-end gap-2">
                        <x-button variant="secondary" type="button" x-on:click="$dispatch('close-modal', 'remove-member')">Cancelar</x-button>
                        <x-button variant="danger">Remover</x-button>
                    </div>
                </form>
            </x-modal>
        @endif
    </div>
</x-app-layout>
