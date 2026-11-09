@php $invalid = $attributes->has('name') && $errors->has($attributes->get('name')); @endphp

<textarea aria-invalid="{{ $invalid ? 'true' : 'false' }}" {{ $attributes->merge(['class' => 'form-input min-h-[6rem] resize-y', 'rows' => 4]) }}>{{ $slot }}</textarea>
