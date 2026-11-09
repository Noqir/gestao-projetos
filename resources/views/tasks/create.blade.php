<x-app-layout>
    <x-slot name="title">Nova tarefa</x-slot>

    <div class="mx-auto max-w-2xl">
        <x-page-header title="Nova tarefa" :subtitle="$project->name">
            <x-slot name="breadcrumb">
                <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center gap-1 hover:text-ink"><x-icon name="arrow-left" class="size-4" />Voltar ao quadro</a>
            </x-slot>
        </x-page-header>

        <x-card>
            <form method="POST" action="{{ route('projects.tasks.store', $project) }}">
                @csrf
                @include('tasks.partials.form')
                <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
                    <x-button variant="ghost" :href="route('projects.show', $project)">Cancelar</x-button>
                    <x-button icon="plus">Criar tarefa</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
