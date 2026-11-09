<x-app-layout>
    <x-slot name="title">Editar {{ $project->name }}</x-slot>

    <div class="mx-auto max-w-2xl">
        <x-page-header title="Editar projeto">
            <x-slot name="breadcrumb">
                <a href="{{ route('projects.index') }}" class="hover:text-ink">Projetos</a>
                <x-icon name="chevron-right" class="size-3.5" />
                <a href="{{ route('projects.show', $project) }}" class="truncate hover:text-ink">{{ $project->name }}</a>
                <x-icon name="chevron-right" class="size-3.5" />
                <span>Editar</span>
            </x-slot>
        </x-page-header>

        <x-card>
            <form method="POST" action="{{ route('projects.update', $project) }}">
                @csrf @method('PUT')
                @include('projects.form')
                <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
                    <x-button variant="ghost" :href="route('projects.show', $project)">Cancelar</x-button>
                    <x-button icon="check">Salvar alterações</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
