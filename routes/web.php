<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// Todas as rotas do sistema exigem sessão autenticada; o acesso a cada
// projeto/tarefa é decidido pelas Policies (ProjectPolicy, TaskPolicy).
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('projects', ProjectController::class);
    Route::patch('/projects/{project}/archive', [ProjectController::class, 'archive'])->name('projects.archive');
    Route::patch('/projects/{project}/restore', [ProjectController::class, 'restore'])->name('projects.restore');

    Route::get('/projects/{project}/members', [MemberController::class, 'index'])->name('projects.members.index');
    Route::post('/projects/{project}/members', [MemberController::class, 'store'])->name('projects.members.store');
    Route::delete('/projects/{project}/members/{user}', [MemberController::class, 'destroy'])->name('projects.members.destroy');

    Route::get('/projects/{project}/activity', ActivityController::class)->name('projects.activity');

    // scoped(): a tarefa precisa pertencer ao projeto da URL (senão 404).
    Route::resource('projects.tasks', TaskController::class)->except('index')->scoped();
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status');
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])->name('tasks.comments.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
