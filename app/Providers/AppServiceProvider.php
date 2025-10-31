<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Src\Application\TaskList\UseCases\GetTaskListList;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Sidebar에 TaskList 목록 자동 전달
        View::composer('components.layout.sidebar', function ($view) {
            $getTaskListList = app(GetTaskListList::class);
            $result = $getTaskListList->execute(userId: null); // 게스트 모드
            $view->with('taskLists', $result->taskLists);
        });
    }
}
