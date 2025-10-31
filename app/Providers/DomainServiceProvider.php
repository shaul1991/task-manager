<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Infrastructure\Task\Repositories\EloquentTaskRepository;
use Src\Domain\TaskList\Repositories\TaskListRepositoryInterface;
use Src\Infrastructure\TaskList\Repositories\EloquentTaskListRepository;
use Src\Domain\TaskGroup\Repositories\TaskGroupRepositoryInterface;
use Src\Infrastructure\TaskGroup\Repositories\EloquentTaskGroupRepository;

/**
 * Domain Service Provider
 *
 * Domain Layer의 인터페이스와 Infrastructure Layer의 구현체를 바인딩합니다.
 */
class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Task Repository 바인딩
        $this->app->bind(
            TaskRepositoryInterface::class,
            EloquentTaskRepository::class
        );

        // TaskList Repository 바인딩
        $this->app->bind(
            TaskListRepositoryInterface::class,
            EloquentTaskListRepository::class
        );

        // TaskGroup Repository 바인딩
        $this->app->bind(
            TaskGroupRepositoryInterface::class,
            EloquentTaskGroupRepository::class
        );

        // 향후 User Repository 바인딩 추가 예정
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
