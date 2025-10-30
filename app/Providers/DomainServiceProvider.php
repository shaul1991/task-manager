<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Infrastructure\Task\Repositories\EloquentTaskRepository;

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

        // 향후 User, Group Repository 바인딩 추가 예정
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
