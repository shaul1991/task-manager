<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskListController;
use App\Http\Controllers\TaskGroupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main');
})->name('main');

// Task 라우트 (웹 + AJAX 겸용)
Route::resource('tasks', TaskController::class);

// TaskGroup 라우트 (AJAX API)
Route::patch('task-groups/reorder', [TaskGroupController::class, 'reorder'])
    ->name('task-groups.reorder');
Route::resource('task-groups', TaskGroupController::class)->only([
    'index', 'show', 'store', 'update', 'destroy'
]);

// TaskList 순서 변경 및 그룹 이동 (resource 보다 먼저 정의)
Route::patch('task-lists/reorder', [TaskListController::class, 'reorder'])
    ->name('task-lists.reorder');
Route::patch('task-lists/{id}/move', [TaskListController::class, 'move'])
    ->name('task-lists.move');

// TaskList 라우트 (웹 + AJAX 겸용)
Route::resource('task-lists', TaskListController::class);
