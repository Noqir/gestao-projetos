<div class="space-y-5">
    <x-field label="Título" for="title">
        <x-text-input id="title" name="title" :value="old('title', $task->title)" maxlength="160" autofocus placeholder="O que precisa ser feito?" />
    </x-field>

    <x-field label="Descrição" for="description" optional>
        <x-textarea id="description" name="description" rows="5" placeholder="Detalhes, critérios de aceite, links...">{{ old('description', $task->description) }}</x-textarea>
    </x-field>

    <div class="grid gap-5 sm:grid-cols-2">
        <x-field label="Status" for="status">
            <x-select id="status" name="status" :options="$statuses" :selected="old('status', $task->status?->value)" />
        </x-field>
        <x-field label="Prioridade" for="priority">
            <x-select id="priority" name="priority" :options="$priorities" :selected="old('priority', $task->priority?->value ?? 'medium')" />
        </x-field>
        <x-field label="Responsável" for="assignee_id" error="assignee_id">
            <x-select id="assignee_id" name="assignee_id" placeholder="Ninguém" :options="$project->members->pluck('name', 'id')->all()" :selected="old('assignee_id', $task->assignee_id)" />
        </x-field>
        <x-field label="Prazo" for="due_date" optional>
            <x-text-input id="due_date" type="date" name="due_date" :value="old('due_date', $task->due_date?->format('Y-m-d'))" />
        </x-field>
    </div>
</div>
