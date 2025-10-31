<?php

declare(strict_types=1);

namespace Src\Application\TaskGroup\UseCases;

use Src\Application\TaskGroup\DTOs\UpdateTaskGroupOrderDTO;
use Src\Domain\TaskGroup\Repositories\TaskGroupRepositoryInterface;

/**
 * UpdateTaskGroupOrder UseCase
 *
 * TaskGroup의 순서를 변경합니다
 */
final readonly class UpdateTaskGroupOrder
{
    public function __construct(
        private TaskGroupRepositoryInterface $taskGroupRepository
    ) {
    }

    /**
     * Execute the use case
     *
     * @param UpdateTaskGroupOrderDTO $dto
     * @return void
     */
    public function execute(UpdateTaskGroupOrderDTO $dto): void
    {
        // Repository를 통해 순서 업데이트
        $this->taskGroupRepository->updateOrders($dto->orderMap);
    }
}
