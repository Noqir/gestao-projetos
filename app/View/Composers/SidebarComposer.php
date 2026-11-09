<?php

namespace App\View\Composers;

use App\Services\ProjectService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Fornece os projetos ativos do usuário para a navegação lateral,
 * sem que o layout Blade precise consultar o banco.
 */
class SidebarComposer
{
    public function __construct(private ProjectService $projects) {}

    public function compose(View $view): void
    {
        $view->with('sidebarProjects', Auth::check() ? $this->projects->sidebarFor(Auth::user()) : collect());
    }
}
