<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskListController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main');
})->name('main');

// Task 라우트 (웹 + AJAX 겸용)
Route::resource('tasks', TaskController::class);

// TaskList 라우트 (웹 + AJAX 겸용)
Route::resource('task-lists', TaskListController::class);
