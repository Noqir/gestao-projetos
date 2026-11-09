<x-app-layout>
    <x-slot name="title">Editar tarefa</x-slot>

    <div class="mx-auto max-w-2xl">
        <x-page-header title="Editar tarefa" :subtitle="$project->name">
            <x-slot name="breadcrumb">
                <a href="{{ route('projects.tasks.show', [$project, $task]) }}" class="inline-flex items-center gap-1 hover:text-ink"><x-icon name="arrow-left" class="size-4" />Voltar à tarefa</a>
            </x-slot>
        </x-page-header>

        <x-card>
            <form method="POST" action="{{ route('projects.tasks.update', [$project, $task]) }}">
                @csrf @method('PUT')
                @include('tasks.partials.form')
                <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
                    <x-button variant="ghost" :href="route('projects.tasks.show', [$project, $task])">Cancelar</x-button>
                    <x-button icon="check">Salvar alterações</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
