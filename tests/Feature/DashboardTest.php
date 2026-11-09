<?php

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Support\Facades\DB;

// RF06: os números do painel conferem com a contagem real no banco.

it('calcula indicadores iguais à contagem real das tarefas', function () {
    [$project, $owner, $member] = projectWithTeam();
    Task::factory()->count(3)->for($project)->status(TaskStatus::Todo)->create(['assignee_id' => $member->id, 'due_date' => null]);
    Task::factory()->count(2)->for($project)->status(TaskStatus::Doing)->create(['assignee_id' => $owner->id, 'due_date' => null]);
    Task::factory()->count(4)->for($project)->status(TaskStatus::Done)->create(['assignee_id' => $member->id]);
    Task::factory()->for($project)->overdue()->create();

    // Projeto arquivado e projeto alheio não entram no painel
    [$archived] = projectWithTeam(['archived' => true]);
    $archived->members()->attach($owner->id, ['role' => 'member']);
    Task::factory()->count(5)->for($archived)->create();
    [$foreign] = projectWithTeam();
    Task::factory()->count(7)->for($foreign)->create();

    $data = app(DashboardService::class)->for($owner);

    $expected = DB::table('tasks')->where('project_id', $project->id)
        ->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

    expect($data['total'])->toBe(10)
        ->and($data['byStatus'])->toBe(['todo' => 4, 'doing' => 2, 'review' => 0, 'done' => 4])
        ->and($data['byStatus']['todo'])->toBe((int) $expected['todo'])
        ->and($data['overdue'])->toBe(1)
        ->and($data['completion'])->toBe(40);

    $memberLoad = $data['workload']->firstWhere('id', $member->id);
    expect($memberLoad->open_tasks)->toBe(3)->and($memberLoad->done_tasks)->toBe(4);
});

it('mostra o painel preenchido e o estado vazio para quem não tem projetos', function () {
    [$project, $owner] = projectWithTeam();
    Task::factory()->for($project)->create(['title' => 'Tarefa do painel', 'assignee_id' => $owner->id, 'status' => 'todo']);

    $this->actingAs($owner)->get(route('dashboard'))
        ->assertOk()->assertSee('Tarefas por status')->assertSee('Tarefa do painel');

    $this->actingAs(User::factory()->create())->get(route('dashboard'))
        ->assertOk()->assertSee('Seu painel ainda está vazio');
});
