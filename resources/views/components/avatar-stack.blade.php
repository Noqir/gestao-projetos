@props(['users', 'max' => 4, 'size' => 'sm'])

<div {{ $attributes->merge(['class' => 'flex -space-x-1.5']) }}>
    @foreach ($users->take($max) as $user)
        <x-avatar :user="$user" :size="$size" />
    @endforeach
    @if ($users->count() > $max)
        <span class="inline-flex size-7 items-center justify-center rounded-full bg-surface-muted text-[11px] font-semibold text-ink-muted ring-2 ring-surface">+{{ $users->count() - $max }}</span>
    @endif
</div>
