{{-- Lista de opções. `options` é um array valor => rótulo; `selected` o valor atual. --}}
@props(['options' => [], 'selected' => null, 'placeholder' => null])

@php $invalid = $attributes->has('name') && $errors->has($attributes->get('name')); @endphp

<select aria-invalid="{{ $invalid ? 'true' : 'false' }}" {{ $attributes->merge(['class' => 'form-input pr-9']) }}>
    @if ($placeholder !== null)
        <option value="">{{ $placeholder }}</option>
    @endif
    @foreach ($options as $value => $label)
        <option value="{{ $value }}" @selected((string) $selected === (string) $value)>{{ $label }}</option>
    @endforeach
</select>
