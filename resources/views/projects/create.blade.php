<x-app-layout>
    <x-slot name="title">Novo projeto</x-slot>

    <div class="mx-auto max-w-2xl">
        <x-page-header title="Novo projeto" subtitle="Você será o proprietário e poderá convidar membros em seguida.">
            <x-slot name="breadcrumb">
                <a href="{{ route('projects.index') }}" class="hover:text-ink">Projetos</a>
                <x-icon name="chevron-right" class="size-3.5" />
                <span>Novo</span>
            </x-slot>
        </x-page-header>

        <x-card>
            <form method="POST" action="{{ route('projects.store') }}">
                @csrf
                @include('projects.form')
                <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
                    <x-button variant="ghost" :href="route('projects.index')">Cancelar</x-button>
                    <x-button icon="check">Criar projeto</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
