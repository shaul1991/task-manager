<?php

declare(strict_types=1);

namespace Src\Application\TaskList\UseCases;

use Src\Application\TaskList\DTOs\UpdateTaskListOrderDTO;
use Src\Domain\TaskList\Repositories\TaskListRepositoryInterface;

/**
 * UpdateTaskListOrder UseCase
 *
 * TaskList의 순서를 변경합니다
 */
final readonly class UpdateTaskListOrder
{
    public function __construct(
        private TaskListRepositoryInterface $taskListRepository
    ) {
    }

    /**
     * Execute the use case
     *
     * @param UpdateTaskListOrderDTO $dto
     * @return void
     */
    public function execute(UpdateTaskListOrderDTO $dto): void
    {
        // Repository를 통해 순서 업데이트
        $this->taskListRepository->updateOrders($dto->orderMap);
    }
}
