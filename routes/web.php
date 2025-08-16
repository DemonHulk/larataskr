<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;

Route::get('/', [TaskController::class, 'index'])->name('tasks.index');

Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');

Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

// Reordenamiento por drag & drop (AJAX)
Route::post('/tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
