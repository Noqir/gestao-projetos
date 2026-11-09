<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\View\Composers\SidebarComposer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Datas relativas e nomes de meses em português ("há 2 horas", "12 nov").
        Carbon::setLocale(config('app.locale'));
        // "agora" em vez de "há 0 segundo"; "ontem"/"anteontem" em vez de "há 1 dia".
        Carbon::setHumanDiffOptions(Carbon::JUST_NOW | Carbon::ONE_DAY_WORDS | Carbon::TWO_DAY_WORDS);

        // activity_logs.subject_type grava "task"/"project" em vez do nome da classe PHP.
        Relation::enforceMorphMap([
            'project' => Project::class,
            'task' => Task::class,
            'comment' => Comment::class,
            'user' => User::class,
        ]);

        // A barra lateral recebe a lista de projetos do usuário (via ProjectService).
        View::composer('layouts.navigation', SidebarComposer::class);

        // Em desenvolvimento, falha cedo em consultas N+1 (lazy loading) e atributos inexistentes.
        Model::shouldBeStrict(! $this->app->isProduction());

        // No ambiente local (demonstração) um N+1 vira aviso no log em vez de erro na tela;
        // nos testes (APP_ENV=testing) continua lançando exceção.
        if ($this->app->isLocal()) {
            Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
                Log::warning('Lazy loading detectado: '.$model::class."::{$relation}");
            });
        }
    }
}
