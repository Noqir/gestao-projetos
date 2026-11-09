@props(['disabled' => false])

@php $invalid = $attributes->has('name') && $errors->has($attributes->get('name')); @endphp

<input @disabled($disabled) aria-invalid="{{ $invalid ? 'true' : 'false' }}" {{ $attributes->merge(['class' => 'form-input', 'type' => 'text']) }}>
