<?php

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            // Se o responsável for excluído, a tarefa continua, sem responsável.
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 160);
            $table->text('description')->nullable();
            $table->enum('status', TaskStatus::values())->default(TaskStatus::Todo->value);
            $table->enum('priority', TaskPriority::values())->default(TaskPriority::Medium->value);
            $table->date('due_date')->nullable()->index();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            // Monta o Kanban: tarefas de um projeto, por coluna, na ordem.
            $table->index(['project_id', 'status', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
