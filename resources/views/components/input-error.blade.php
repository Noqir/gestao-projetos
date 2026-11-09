@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'space-y-1 text-xs font-medium text-rose-600 dark:text-rose-400']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-center gap-1"><x-icon name="warning" class="size-3.5 shrink-0" />{{ $message }}</li>
        @endforeach
    </ul>
@endif
