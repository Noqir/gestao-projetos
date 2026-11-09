<?php

use App\Enums\TaskStatus;
use App\Models\ActivityLog;

it('descreve a movimentação de tarefa em português', function () {
    $log = new ActivityLog([
        'action' => 'task.moved',
        'properties' => ['title' => 'Login', 'from' => 'todo', 'to' => 'doing'],
    ]);

    expect($log->message())->toBe('moveu “Login” de A fazer para Em andamento');
});

it('traduz todos os status do Kanban', function () {
    expect(array_map(fn ($s) => $s->label(), TaskStatus::cases()))
        ->toBe(['A fazer', 'Em andamento', 'Em revisão', 'Concluída']);
});
