{{-- Campo de formulário: rótulo + controle (slot) + dica + erro de validação. --}}
@props(['label', 'for', 'error' => null, 'bag' => 'default', 'hint' => null, 'optional' => false])

@php
    $errorKey = $error ?? $for;
    $bagErrors = $errors->getBag($bag);
@endphp

<div {{ $attributes }}>
    <label for="{{ $for }}" class="form-label">
        {{ $label }}
        @if ($optional)<span class="font-normal text-ink-subtle">(opcional)</span>@endif
    </label>
    {{ $slot }}
    @if ($hint && ! $bagErrors->has($errorKey))
        <p class="mt-1.5 text-xs text-ink-subtle">{{ $hint }}</p>
    @endif
    <x-input-error :messages="$bagErrors->get($errorKey)" class="mt-1.5" />
</div>
