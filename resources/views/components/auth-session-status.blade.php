@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'flex items-start gap-2 rounded-lg bg-emerald-50 px-3 py-2.5 text-sm font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300']) }}>
        <x-icon name="check-circle" class="mt-px size-4 shrink-0" />
        <span>{{ $status }}</span>
    </div>
@endif
