{{-- Modal "Nova tarefa" do quadro. Reabre sozinho se a validação falhar. --}}
@php $reopen = $errors->any() && old('_form') === 'create-task'; @endphp

<x-modal name="create-task" :show="$reopen" maxWidth="xl">
    <x-slot name="title">Nova tarefa</x-slot>

    <form method="POST" action="{{ route('projects.tasks.store', $project) }}"
        x-data="{ status: @js(old('status', 'todo')) }" @task-status.window="status = $event.detail">
        @csrf
        <input type="hidden" name="_form" value="create-task">
        <input type="hidden" name="status" :value="status">

        <div class="space-y-5 p-6">
            <x-field label="Título" for="task_title" error="title">
                <x-text-input id="task_title" name="title" :value="$reopen ? old('title') : ''" maxlength="160" placeholder="O que precisa ser feito?" autofocus />
            </x-field>

            <x-field label="Descrição" for="task_description" error="description" optional>
                <x-textarea id="task_description" name="description" rows="3" placeholder="Detalhes, critérios de aceite, links...">{{ $reopen ? old('description') : '' }}</x-textarea>
            </x-field>

            <div class="grid gap-5 sm:grid-cols-3">
                <x-field label="Responsável" for="task_assignee" error="assignee_id">
                    <x-select id="task_assignee" name="assignee_id" placeholder="Ninguém"
                        :options="$project->members->pluck('name', 'id')->all()" :selected="$reopen ? old('assignee_id') : ''" />
                </x-field>
                <x-field label="Prioridade" for="task_priority" error="priority">
                    <x-select id="task_priority" name="priority" :options="$priorities" :selected="$reopen ? old('priority') : 'medium'" />
                </x-field>
                <x-field label="Prazo" for="task_due_date" error="due_date">
                    <x-text-input id="task_due_date" type="date" name="due_date" :value="$reopen ? old('due_date') : ''" />
                </x-field>
            </div>

            <p class="flex items-center gap-1.5 text-xs text-ink-subtle">
                <x-icon name="board" class="size-4" />
                Será criada na coluna
                <span class="font-medium text-ink-muted" x-text="@js(\App\Enums\TaskStatus::options())[status]"></span>
            </p>
        </div>

        <div class="flex justify-end gap-2 border-t border-line bg-surface-muted/50 px-6 py-4">
            <x-button variant="secondary" type="button" x-on:click="$dispatch('close-modal', 'create-task')">Cancelar</x-button>
            <x-button icon="plus">Criar tarefa</x-button>
        </div>
    </form>
</x-modal>
