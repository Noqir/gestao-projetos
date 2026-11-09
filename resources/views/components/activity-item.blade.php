{{-- Item da linha do tempo. `showProject` exibe o nome do projeto (usado no painel). --}}
@props(['activity', 'showProject' => false, 'last' => false])

@php [$icon, $color] = $activity->icon(); @endphp

<li class="relative flex gap-3 pb-6 last:pb-0">
    @unless ($last)
        <span class="absolute left-4 top-9 -bottom-0 w-px bg-line" aria-hidden="true"></span>
    @endunless
    <span class="relative flex size-8 shrink-0 items-center justify-center rounded-full text-white ring-4 ring-surface {{ $color }}">
        <x-icon :name="$icon" class="size-4" />
    </span>
    <div class="min-w-0 flex-1 pt-1">
        <p class="text-sm text-ink-muted">
            <span class="font-semibold text-ink">{{ $activity->user?->name ?? 'Usuário removido' }}</span>
            {{ $activity->message() }}
            @if ($showProject && $activity->relationLoaded('project'))
                <span class="text-ink-subtle">em</span>
                <a href="{{ route('projects.show', $activity->project_id) }}" class="font-medium text-ink hover:text-brand-600">{{ $activity->project->name }}</a>
            @endif
        </p>
        <time class="mt-0.5 block text-xs text-ink-subtle" datetime="{{ $activity->created_at->toIso8601String() }}" title="{{ $activity->created_at->format('d/m/Y H:i') }}">
            {{ $activity->created_at->diffForHumans() }}
        </time>
    </div>
</li>
