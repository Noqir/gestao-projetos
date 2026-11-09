{{-- Navegação lateral. $sidebarProjects é fornecido pelo SidebarComposer (via ProjectService). --}}
<div class="flex h-full flex-col">
    <div class="flex h-16 items-center gap-2.5 px-5">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
            <x-application-logo class="size-8" />
            <div class="leading-tight">
                <p class="text-sm font-semibold text-ink">Gestão de Projetos</p>
                <p class="text-[11px] text-ink-subtle">Equipe 1</p>
            </div>
        </a>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-4">
        <div class="space-y-1">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="dashboard">Painel</x-nav-link>
            <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.index', 'projects.create')" icon="folder">Projetos</x-nav-link>
        </div>

        <div>
            <div class="mb-2 flex items-center justify-between px-3">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-ink-subtle">Meus projetos</p>
                <a href="{{ route('projects.create') }}" class="rounded p-0.5 text-ink-subtle transition hover:bg-surface-muted hover:text-ink" title="Novo projeto" aria-label="Novo projeto">
                    <x-icon name="plus" class="size-4" />
                </a>
            </div>
            <div class="space-y-0.5">
                @forelse ($sidebarProjects ?? [] as $sidebarProject)
                    @php $isCurrent = request()->route('project')?->is($sidebarProject); @endphp
                    <a href="{{ route('projects.show', $sidebarProject) }}"
                        @class([
                            'group flex items-center gap-3 rounded-lg px-3 py-1.5 text-sm transition',
                            'bg-surface-muted font-medium text-ink' => $isCurrent,
                            'text-ink-muted hover:bg-surface-muted hover:text-ink' => ! $isCurrent,
                        ])>
                        <span class="size-2 shrink-0 rounded-full {{ $sidebarProject->colorClass() }}"></span>
                        <span class="truncate">{{ $sidebarProject->name }}</span>
                    </a>
                @empty
                    <p class="px-3 py-1 text-xs text-ink-subtle">Nenhum projeto ativo.</p>
                @endforelse
            </div>
        </div>
    </nav>

    <div class="border-t border-line p-3">
        <div class="flex items-center gap-1">
            <x-dropdown align="left" width="w-60" direction="up">
                <x-slot name="trigger">
                    <button type="button" class="flex min-w-0 flex-1 items-center gap-3 rounded-lg px-2 py-2 text-left transition hover:bg-surface-muted">
                        <x-avatar :user="Auth::user()" size="md" />
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-medium text-ink">{{ Auth::user()->name }}</span>
                            <span class="block truncate text-xs text-ink-subtle">{{ Auth::user()->email }}</span>
                        </span>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')" icon="user">Meu perfil</x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link as="button" icon="logout">Sair</x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>

            <button type="button" x-data="themeToggle" @click="toggle()"
                class="rounded-lg p-2 text-ink-subtle transition hover:bg-surface-muted hover:text-ink"
                :title="dark ? 'Usar tema claro' : 'Usar tema escuro'" aria-label="Alternar tema">
                <x-icon name="moon" class="size-5" x-show="!dark" />
                <x-icon name="sun" class="size-5" x-show="dark" x-cloak />
            </button>
        </div>
    </div>
</div>
