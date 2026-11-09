{{-- Campos compartilhados por criar e editar projeto. --}}
<div class="space-y-5">
    <x-field label="Nome do projeto" for="name">
        <x-text-input id="name" name="name" :value="old('name', $project->name)" required maxlength="120" autofocus placeholder="Ex.: Lançamento do App Mobile" />
    </x-field>

    <x-field label="Descrição" for="description" optional hint="Explique o objetivo do projeto para quem for convidado.">
        <x-textarea id="description" name="description" rows="4" placeholder="Qual é o objetivo deste projeto?">{{ old('description', $project->description) }}</x-textarea>
    </x-field>

    <x-field label="Data de entrega" for="due_date" optional class="sm:max-w-xs">
        <x-text-input id="due_date" type="date" name="due_date" :value="old('due_date', $project->due_date?->format('Y-m-d'))" />
    </x-field>
</div>
